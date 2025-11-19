<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\ServicoExtra;
use App\Models\NotaCobranca;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckinController extends Controller
{
    public function index()
    {
        $reservasCheckin = Reserva::where('status', 'confirmada')
            ->whereDate('data_entrada', '<=', Carbon::today())
            ->with(['cliente', 'quarto'])
            ->orderBy('data_entrada')
            ->get();

        $reservasCheckout = Reserva::where('status', 'checkin')
            ->whereDate('data_saida', '<=', Carbon::today())
            ->with(['cliente', 'quarto'])
            ->orderBy('data_saida')
            ->get();

        $servicos = ServicoExtra::all();

        return view('checkin.index', compact('reservasCheckin', 'reservasCheckout', 'servicos'));
    }

    public function realizarCheckin($id)
    {
        $reserva = Reserva::findOrFail($id);
        
        if ($reserva->status !== 'confirmada') {
            return redirect()->route('admin.checkin.index')->with('error', 'Apenas reservas confirmadas podem fazer check-in!');
        }

        $reserva->update([
            'status' => 'checkin',
            'checkin_em' => now(),
            'checkin_por' => auth()->id(),
        ]);

        $reserva->quarto->update(['estado' => 'ocupado']);

        return redirect()->route('admin.checkin.index')->with('success', 'Check-in realizado com sucesso!');
    }

    public function realizarCheckout(Request $request, $id)
    {
        $reserva = Reserva::with(['cliente', 'quarto', 'servicos.servico'])->findOrFail($id);
        
        if ($reserva->status !== 'checkin') {
            return redirect()->route('admin.checkin.index')->with('error', 'Apenas reservas em check-in podem fazer check-out!');
        }

        $request->validate([
            'tipo_pagamento' => 'required|in:dinheiro,cartao',
        ]);

        $reserva->update([
            'status' => 'checkout',
            'checkout_em' => now(),
            'checkout_por' => auth()->id(),
            'tipo_pagamento' => $request->tipo_pagamento,
            'valor_total' => $reserva->calcularValorTotal(),
        ]);

        $reserva->quarto->update(['estado' => 'limpeza']);

        // Se for empresa, gerar nota de cobrança
        if ($reserva->cliente->isEmpresa()) {
            $numeroNota = 'NC-' . str_pad(NotaCobranca::max('id') + 1, 6, '0', STR_PAD_LEFT);
            NotaCobranca::create([
                'reserva_id' => $reserva->id,
                'empresa_id' => $reserva->cliente_id,
                'valor_total' => $reserva->valor_total,
                'data_emissao' => now(),
                'numero_nota' => $numeroNota,
            ]);
        }

        return redirect()->route('admin.checkin.index')->with('success', 'Check-out realizado com sucesso!');
    }

    public function adicionarServico(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);
        
        if ($reserva->status !== 'checkin') {
            return response()->json(['error' => 'Apenas reservas em check-in podem receber serviços!'], 400);
        }

        $request->validate([
            'servico_id' => 'required|exists:servico_extras,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        $servico = ServicoExtra::findOrFail($request->servico_id);
        
        \App\Models\ReservaServico::create([
            'reserva_id' => $reserva->id,
            'servico_id' => $request->servico_id,
            'quantidade' => $request->quantidade,
            'subtotal' => $servico->preco * $request->quantidade,
        ]);

        return response()->json(['success' => true, 'message' => 'Serviço adicionado com sucesso!']);
    }
}


