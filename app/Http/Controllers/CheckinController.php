<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Stay;
use App\Models\ServicoExtra;
use App\Models\NotaCobranca;
use App\Models\Cliente;
use App\Models\Quarto;
use App\Services\CheckinService;
use App\Http\Requests\StoreWalkInCheckinRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckinController extends Controller
{
    protected $checkinService;

    public function __construct(CheckinService $checkinService)
    {
        $this->checkinService = $checkinService;
    }

    /**
     * Exibe a página de check-in/check-out
     * Inclui reservas confirmadas aguardando check-in, check-ins diretos e estadias ativas
     */
    public function index()
    {
        // Reservas confirmadas aguardando check-in
        $reservasCheckin = Reserva::where('status', 'confirmada')
            ->whereDate('data_entrada', '<=', Carbon::today())
            ->with(['cliente', 'quarto'])
            ->orderBy('data_entrada')
            ->get();

        // Reservas em check-in aguardando check-out
        $reservasCheckout = Reserva::where('status', 'checkin')
            ->whereDate('data_saida', '<=', Carbon::today())
            ->with(['cliente', 'quarto'])
            ->orderBy('data_saida')
            ->get();

        // Estadias diretas ativas (walk-in) aguardando check-out
        $staysCheckout = Stay::where('status', 'active')
            ->where('expected_check_out_at', '<=', now())
            ->with(['guest', 'room', 'createdBy'])
            ->orderBy('expected_check_out_at')
            ->get();

        $servicos = ServicoExtra::all();

        return view('checkin.index', compact('reservasCheckin', 'reservasCheckout', 'staysCheckout', 'servicos'));
    }

    /**
     * Realiza check-in direto (walk-in) sem reserva prévia
     * Usa o Service para encapsular a lógica de negócio
     */
    public function realizarCheckinDireto(StoreWalkInCheckinRequest $request)
    {
        try {
            $stay = $this->checkinService->createWalkInCheckin($request->validated());

            return redirect()->route('admin.checkin.index')
                ->with('success', 'Check-in direto realizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('admin.checkin.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Realiza check-out de uma estadia direta (walk-in)
     */
    public function realizarCheckoutStay(Request $request, $id)
    {
        $request->validate([
            'payment_type' => 'required|in:dinheiro,cartao',
        ]);

        try {
            $stay = $this->checkinService->checkout($id, [
                'payment_type' => $request->payment_type,
            ]);

            // Se o cliente for empresa, gerar nota de cobrança
            if ($stay->guest->isEmpresa()) {
                $numeroNota = 'NC-' . str_pad(NotaCobranca::max('id') + 1, 6, '0', STR_PAD_LEFT);
                NotaCobranca::create([
                    'reserva_id' => null, // Não há reserva para walk-in
                    'empresa_id' => $stay->guest_id,
                    'valor_total' => $stay->total_amount,
                    'data_emissao' => now(),
                    'numero_nota' => $numeroNota,
                ]);
            }

            return redirect()->route('admin.checkin.index')
                ->with('success', 'Check-out realizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('admin.checkin.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Cancela uma estadia direta ativa
     */
    public function cancelarStay($id)
    {
        try {
            $this->checkinService->cancel($id);

            return redirect()->route('admin.checkin.index')
                ->with('success', 'Estadia cancelada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('admin.checkin.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Retorna quartos disponíveis para check-in direto
     */
    public function getQuartosDisponiveis()
    {
        $quartos = $this->checkinService->getAvailableRooms();

        return response()->json($quartos);
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


