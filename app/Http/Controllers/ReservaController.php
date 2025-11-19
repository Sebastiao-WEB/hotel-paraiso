<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Quarto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $query = Reserva::with(['cliente', 'quarto']);

        if ($request->filled('search')) {
            $query->whereHas('cliente', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data')) {
            $query->whereDate('data_entrada', $request->data);
        }

        $reservas = $query->orderBy('data_entrada', 'desc')->paginate(10);

        return view('reservas.index', compact('reservas'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $quartosDisponiveis = Quarto::where('estado', 'disponivel')->get();
        $reserva = null;
        
        return view('reservas.form', compact('clientes', 'quartosDisponiveis', 'reserva'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'quarto_id' => 'required|exists:quartos,id',
            'data_entrada' => 'required|date|after_or_equal:today',
            'data_saida' => 'required|date|after:data_entrada',
            'tipo_pagamento' => 'nullable|in:dinheiro,cartao',
        ]);

        $quarto = Quarto::findOrFail($validated['quarto_id']);
        $dias = Carbon::parse($validated['data_entrada'])->diffInDays(Carbon::parse($validated['data_saida']));
        $valorTotal = $dias * $quarto->preco_diaria;

        $reserva = Reserva::create([
            ...$validated,
            'valor_total' => $valorTotal,
            'status' => 'pendente',
            'criado_por' => auth()->id(),
        ]);

        return redirect()->route('admin.reservas.index')->with('success', 'Reserva criada com sucesso!');
    }

    public function show($id)
    {
        $reserva = Reserva::with(['cliente', 'quarto', 'servicos.servico', 'criadoPor', 'checkinPor', 'checkoutPor'])->findOrFail($id);
        return view('reservas.show', compact('reserva'));
    }

    public function edit($id)
    {
        $reserva = Reserva::findOrFail($id);
        $clientes = Cliente::orderBy('nome')->get();
        $quartosDisponiveis = Quarto::where('estado', 'disponivel')
            ->orWhere('id', $reserva->quarto_id)
            ->get();
        
        return view('reservas.form', compact('reserva', 'clientes', 'quartosDisponiveis'));
    }

    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'quarto_id' => 'required|exists:quartos,id',
            'data_entrada' => 'required|date',
            'data_saida' => 'required|date|after:data_entrada',
            'tipo_pagamento' => 'nullable|in:dinheiro,cartao',
        ]);

        $quarto = Quarto::findOrFail($validated['quarto_id']);
        $dias = Carbon::parse($validated['data_entrada'])->diffInDays(Carbon::parse($validated['data_saida']));
        $valorTotal = $dias * $quarto->preco_diaria;

        $reserva->update([
            ...$validated,
            'valor_total' => $valorTotal,
        ]);

        return redirect()->route('admin.reservas.index')->with('success', 'Reserva atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        
        if (!in_array($reserva->status, ['pendente', 'cancelada'])) {
            return redirect()->route('admin.reservas.index')->with('error', 'Não é possível excluir esta reserva!');
        }

        $reserva->delete();

        return redirect()->route('admin.reservas.index')->with('success', 'Reserva excluída com sucesso!');
    }

    public function confirmar($id)
    {
        $reserva = Reserva::findOrFail($id);
        
        if ($reserva->status !== 'pendente') {
            return redirect()->route('admin.reservas.index')->with('error', 'Apenas reservas pendentes podem ser confirmadas!');
        }

        $reserva->update([
            'status' => 'confirmada',
            'confirmado_em' => now(),
        ]);

        $reserva->quarto->update(['estado' => 'reservado']);

        return redirect()->route('admin.reservas.index')->with('success', 'Reserva confirmada com sucesso!');
    }

    public function cancelar($id)
    {
        $reserva = Reserva::findOrFail($id);
        
        if (in_array($reserva->status, ['checkout', 'cancelada'])) {
            return redirect()->route('admin.reservas.index')->with('error', 'Esta reserva não pode ser cancelada!');
        }

        $reserva->update(['status' => 'cancelada']);
        $reserva->quarto->update(['estado' => 'disponivel']);

        return redirect()->route('admin.reservas.index')->with('success', 'Reserva cancelada com sucesso!');
    }

    public function getQuartosDisponiveis(Request $request)
    {
        $dataEntrada = Carbon::parse($request->data_entrada);
        $dataSaida = Carbon::parse($request->data_saida);

        $quartosOcupados = Reserva::where(function($q) use ($dataEntrada, $dataSaida) {
            $q->whereBetween('data_entrada', [$dataEntrada, $dataSaida])
              ->orWhereBetween('data_saida', [$dataEntrada, $dataSaida])
              ->orWhere(function($q2) use ($dataEntrada, $dataSaida) {
                  $q2->where('data_entrada', '<=', $dataEntrada)
                     ->where('data_saida', '>=', $dataSaida);
              });
        })
        ->whereIn('status', ['pendente', 'confirmada', 'checkin'])
        ->pluck('quarto_id');

        $quartos = Quarto::where('estado', 'disponivel')
            ->whereNotIn('id', $quartosOcupados)
            ->get();

        return response()->json($quartos);
    }
}

