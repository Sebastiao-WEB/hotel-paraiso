<?php

namespace App\Http\Controllers;

use App\Models\Quarto;
use App\Models\Reserva;
use App\Models\Stay;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuartoController extends Controller
{
    public function index(Request $request)
    {
        $query = Quarto::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', '%' . $search . '%')
                  ->orWhere('tipo', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $quartos = $query->orderBy('numero')->paginate(10);

        return view('quartos.index', compact('quartos'));
    }

    public function create()
    {
        return view('quartos.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:10|unique:quartos,numero',
            'tipo' => 'required|string|max:255',
            'preco_diaria' => 'required|numeric|min:0',
            'estado' => 'required|in:disponivel,reservado,ocupado,limpeza',
        ]);

        Quarto::create($validated);

        return redirect()->route('quartos.index')->with('success', 'Quarto criado com sucesso!');
    }

    public function edit($id)
    {
        $quarto = Quarto::findOrFail($id);
        return view('quartos.form', compact('quarto'));
    }

    public function update(Request $request, $id)
    {
        $quarto = Quarto::findOrFail($id);

        $validated = $request->validate([
            'numero' => 'required|string|max:10|unique:quartos,numero,' . $quarto->id,
            'tipo' => 'required|string|max:255',
            'preco_diaria' => 'required|numeric|min:0',
            'estado' => 'required|in:disponivel,reservado,ocupado,limpeza',
        ]);

        $quarto->update($validated);

        return redirect()->route('admin.quartos.index')->with('success', 'Quarto atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $quarto = Quarto::findOrFail($id);
        $quarto->delete();

        return redirect()->route('admin.quartos.index')->with('success', 'Quarto excluído com sucesso!');
    }

    /**
     * Exibe detalhes do quarto incluindo informações de ocupação
     */
    public function show($id)
    {
        $quarto = Quarto::findOrFail($id);
        
        // Reserva ativa (em check-in)
        $reservaAtiva = Reserva::where('quarto_id', $quarto->id)
            ->where('status', 'checkin')
            ->where('data_saida', '>=', Carbon::today())
            ->with(['cliente', 'servicos.servico'])
            ->first();
        
        // Estadia direta ativa (walk-in)
        $stayAtiva = Stay::where('room_id', $quarto->id)
            ->where('status', 'active')
            ->with(['guest', 'createdBy'])
            ->first();
        
        // Reservas confirmadas futuras
        $reservasFuturas = Reserva::where('quarto_id', $quarto->id)
            ->where('status', 'confirmada')
            ->where('data_entrada', '>=', Carbon::today())
            ->with(['cliente'])
            ->orderBy('data_entrada')
            ->limit(5)
            ->get();
        
        // Histórico recente (últimas 10 ocupações)
        $historicoReservas = Reserva::where('quarto_id', $quarto->id)
            ->whereIn('status', ['checkout', 'cancelada'])
            ->with(['cliente'])
            ->orderBy('checkout_em', 'desc')
            ->limit(5)
            ->get();
        
        $historicoStays = Stay::where('room_id', $quarto->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['guest'])
            ->orderBy('actual_check_out_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('quartos.show', compact(
            'quarto', 
            'reservaAtiva', 
            'stayAtiva', 
            'reservasFuturas',
            'historicoReservas',
            'historicoStays'
        ));
    }

    /**
     * Altera o estado de um quarto
     */
    public function alterarEstado(Request $request, $id)
    {
        $quarto = Quarto::findOrFail($id);
        
        $validated = $request->validate([
            'estado' => 'required|in:disponivel,reservado,ocupado,limpeza',
        ]);

        $quarto->update(['estado' => $validated['estado']]);

        return response()->json(['success' => true, 'message' => 'Estado atualizado com sucesso!']);
    }
}

