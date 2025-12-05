<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\ServicoExtra;
use App\Models\NotaCobranca;
use App\Models\Cliente;
use App\Models\Quarto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckinController extends Controller
{
    /**
     * Exibe a página de check-in/check-out
     * Inclui reservas confirmadas aguardando check-in e check-ins diretos aguardando check-out
     */
    public function index()
    {
        // Reservas confirmadas aguardando check-in
        $reservasCheckin = Reserva::where('status', 'confirmada')
            ->whereDate('data_entrada', '<=', Carbon::today())
            ->with(['cliente', 'quarto'])
            ->orderBy('data_entrada')
            ->get();

        // Reservas em check-in aguardando check-out (inclui check-ins diretos)
        $reservasCheckout = Reserva::where('status', 'checkin')
            ->whereDate('data_saida', '<=', Carbon::today())
            ->with(['cliente', 'quarto'])
            ->orderBy('data_saida')
            ->get();

        $servicos = ServicoExtra::all();

        return view('checkin.index', compact('reservasCheckin', 'reservasCheckout', 'servicos'));
    }

    /**
     * Realiza check-in direto sem reserva prévia
     * Cria uma reserva com status "checkin" diretamente
     */
    public function realizarCheckinDireto(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'quarto_id' => 'required|exists:quartos,id',
            'data_entrada' => 'required|date|before_or_equal:today',
            'data_saida' => 'required|date|after:data_entrada',
        ]);

        $quarto = Quarto::findOrFail($validated['quarto_id']);
        $dataEntrada = Carbon::parse($validated['data_entrada']);
        $dataSaida = Carbon::parse($validated['data_saida']);

        // Verifica se o quarto está disponível para o período
        if (!$quarto->isDisponivelParaPeriodo($dataEntrada, $dataSaida)) {
            return redirect()->route('admin.checkin.index')
                ->with('error', 'O quarto selecionado não está disponível para o período informado!');
        }

        // Verifica se o quarto não está ocupado
        if ($quarto->isOcupado()) {
            return redirect()->route('admin.checkin.index')
                ->with('error', 'O quarto selecionado está ocupado!');
        }

        // Calcula o valor total
        $dias = $dataEntrada->diffInDays($dataSaida);
        $valorTotal = $dias * $quarto->preco_diaria;

        // Cria a reserva com status "checkin" diretamente
        $reserva = Reserva::create([
            'cliente_id' => $validated['cliente_id'],
            'quarto_id' => $validated['quarto_id'],
            'data_entrada' => $dataEntrada,
            'data_saida' => $dataSaida,
            'status' => 'checkin',
            'valor_total' => $valorTotal,
            'checkin_em' => now(),
            'checkin_por' => auth()->id(),
            'criado_por' => auth()->id(),
        ]);

        // Atualiza o estado do quarto para ocupado
        $quarto->update(['estado' => 'ocupado']);

        return redirect()->route('admin.checkin.index')
            ->with('success', 'Check-in direto realizado com sucesso!');
    }

    /**
     * Realiza check-in de uma reserva confirmada
     * Atualiza o status da reserva para "checkin" e o estado do quarto para "ocupado"
     */
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

    /**
     * Realiza check-out de uma reserva em check-in
     * Atualiza o status da reserva para "checkout" e o estado do quarto para "limpeza"
     * Gera nota de cobrança automaticamente se o cliente for uma empresa
     */
    public function realizarCheckout(Request $request, $id)
    {
        $reserva = Reserva::with(['cliente', 'quarto', 'servicos.servico'])->findOrFail($id);
        
        if ($reserva->status !== 'checkin') {
            return redirect()->route('admin.checkin.index')->with('error', 'Apenas reservas em check-in podem fazer check-out!');
        }

        $request->validate([
            'tipo_pagamento' => 'required|in:dinheiro,cartao',
        ]);

        // Recalcula o valor total incluindo serviços extras
        $valorTotal = $reserva->calcularValorTotal();

        $reserva->update([
            'status' => 'checkout',
            'checkout_em' => now(),
            'checkout_por' => auth()->id(),
            'tipo_pagamento' => $request->tipo_pagamento,
            'valor_total' => $valorTotal,
        ]);

        // Atualiza o estado do quarto para limpeza
        $reserva->quarto->update(['estado' => 'limpeza']);

        // Se for empresa, gerar nota de cobrança automaticamente
        if ($reserva->cliente->isEmpresa()) {
            $numeroNota = 'NC-' . str_pad(NotaCobranca::max('id') + 1, 6, '0', STR_PAD_LEFT);
            NotaCobranca::create([
                'reserva_id' => $reserva->id,
                'empresa_id' => $reserva->cliente_id,
                'valor_total' => $valorTotal,
                'data_emissao' => now(),
                'numero_nota' => $numeroNota,
            ]);
        }

        return redirect()->route('admin.checkin.index')->with('success', 'Check-out realizado com sucesso!');
    }

    /**
     * Adiciona um serviço extra a uma reserva em check-in
     * Calcula o subtotal automaticamente baseado no preço e quantidade
     */
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
        
        // Cria o registro de serviço com cálculo automático do subtotal
        \App\Models\ReservaServico::create([
            'reserva_id' => $reserva->id,
            'servico_id' => $request->servico_id,
            'quantidade' => $request->quantidade,
            'subtotal' => $servico->preco * $request->quantidade,
        ]);

        return response()->json(['success' => true, 'message' => 'Serviço adicionado com sucesso!']);
    }
}


