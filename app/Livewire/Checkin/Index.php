<?php

namespace App\Livewire\Checkin;

use App\Models\Reserva;
use App\Models\Stay;
use App\Models\ServicoExtra;
use App\Models\Cliente;
use App\Models\Quarto;
use App\Services\CheckinService;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $reservaSelecionada = null;
    public $staySelecionada = null;
    public $mostrarCheckin = false;
    public $mostrarCheckout = false;
    public $mostrarCheckoutStay = false;
    public $servicosSelecionados = [];
    public $tipoPagamento = '';
    
    // Propriedades para check-in direto (walk-in)
    public $mostrarCheckinDireto = false;
    public $cliente_id = null;
    public $quarto_id = null;
    public $check_in_at = null;
    public $expected_check_out_at = null;
    public $notes = null;

    protected $checkinService;

    public function boot(CheckinService $checkinService)
    {
        $this->checkinService = $checkinService;
    }

    public function abrirCheckin($reservaId)
    {
        $this->reservaSelecionada = Reserva::with(['cliente', 'quarto', 'servicos.servico'])->findOrFail($reservaId);
        $this->mostrarCheckin = true;
    }

    public function realizarCheckin()
    {
        if (!$this->reservaSelecionada || $this->reservaSelecionada->status !== 'confirmada') {
            session()->flash('error', 'Apenas reservas confirmadas podem fazer check-in!');
            return;
        }

        $this->reservaSelecionada->update([
            'status' => 'checkin',
            'checkin_em' => now(),
            'checkin_por' => auth()->id(),
        ]);

        $this->reservaSelecionada->quarto->update(['estado' => 'ocupado']);

        session()->flash('success', 'Check-in realizado com sucesso!');
        $this->mostrarCheckin = false;
        $this->reservaSelecionada = null;
    }

    public function abrirCheckout($reservaId)
    {
        $this->reservaSelecionada = Reserva::with(['cliente', 'quarto', 'servicos.servico'])->findOrFail($reservaId);
        
        // Recalcular valor total
        $this->reservaSelecionada->load('quarto', 'servicos');
        $this->reservaSelecionada->valor_total = $this->reservaSelecionada->calcularValorTotal();
        $this->reservaSelecionada->save();
        
        $this->mostrarCheckout = true;
    }

    public function realizarCheckout()
    {
        if (!$this->reservaSelecionada || $this->reservaSelecionada->status !== 'checkin') {
            session()->flash('error', 'Apenas reservas em check-in podem fazer check-out!');
            return;
        }

        if (!$this->tipoPagamento) {
            session()->flash('error', 'Selecione a forma de pagamento!');
            return;
        }

        $this->reservaSelecionada->update([
            'status' => 'checkout',
            'checkout_em' => now(),
            'checkout_por' => auth()->id(),
            'tipo_pagamento' => $this->tipoPagamento,
            'valor_total' => $this->reservaSelecionada->calcularValorTotal(),
        ]);

        $this->reservaSelecionada->quarto->update(['estado' => 'limpeza']);

        // Se for empresa, gerar nota de cobrança
        if ($this->reservaSelecionada->cliente->isEmpresa()) {
            $numeroNota = 'NC-' . str_pad(\App\Models\NotaCobranca::max('id') + 1, 6, '0', STR_PAD_LEFT);
            \App\Models\NotaCobranca::create([
                'reserva_id' => $this->reservaSelecionada->id,
                'empresa_id' => $this->reservaSelecionada->cliente_id,
                'valor_total' => $this->reservaSelecionada->valor_total,
                'data_emissao' => now(),
                'numero_nota' => $numeroNota,
            ]);
        }

        session()->flash('success', 'Check-out realizado com sucesso!');
        $this->mostrarCheckout = false;
        $this->reservaSelecionada = null;
        $this->tipoPagamento = '';
    }

    public function adicionarServico($servicoId, $quantidade = 1)
    {
        if (!$this->reservaSelecionada || $this->reservaSelecionada->status !== 'checkin') {
            session()->flash('error', 'Apenas reservas em check-in podem receber serviços!');
            return;
        }

        $servico = ServicoExtra::findOrFail($servicoId);
        
        \App\Models\ReservaServico::create([
            'reserva_id' => $this->reservaSelecionada->id,
            'servico_id' => $servicoId,
            'quantidade' => $quantidade,
            'subtotal' => $servico->preco * $quantidade,
        ]);

        $this->reservaSelecionada->refresh();
        $this->reservaSelecionada->load('servicos.servico');
        session()->flash('success', 'Serviço adicionado com sucesso!');
    }

    /**
     * Abre o modal para check-in direto (walk-in)
     */
    public function abrirCheckinDireto()
    {
        $this->check_in_at = now()->format('Y-m-d\TH:i');
        $this->expected_check_out_at = now()->addDay()->format('Y-m-d\TH:i');
        $this->mostrarCheckinDireto = true;
        $this->reset(['cliente_id', 'quarto_id', 'notes']);
    }

    /**
     * Realiza check-in direto usando o Service
     */
    public function realizarCheckinDireto()
    {
        // Validação
        $this->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'quarto_id' => 'required|exists:quartos,id',
            'check_in_at' => 'nullable|date|before_or_equal:now',
            'expected_check_out_at' => 'required|date|after:check_in_at',
            'notes' => 'nullable|string|max:1000',
        ], [
            'cliente_id.required' => 'Selecione um cliente.',
            'quarto_id.required' => 'Selecione um quarto.',
            'expected_check_out_at.required' => 'Informe a data prevista de saída.',
            'expected_check_out_at.after' => 'A data de saída deve ser posterior à data de entrada.',
        ]);

        try {
            $data = [
                'guest_id' => $this->cliente_id,
                'room_id' => $this->quarto_id,
                'check_in_at' => $this->check_in_at ?? now()->toDateTimeString(),
                'expected_check_out_at' => $this->expected_check_out_at,
                'notes' => $this->notes,
            ];

            $stay = $this->checkinService->createWalkInCheckin($data);

            session()->flash('success', 'Check-in direto realizado com sucesso!');
            $this->mostrarCheckinDireto = false;
            $this->reset(['cliente_id', 'quarto_id', 'check_in_at', 'expected_check_out_at', 'notes']);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * Abre modal de check-out para estadia direta
     */
    public function abrirCheckoutStay($stayId)
    {
        $this->staySelecionada = Stay::with(['guest', 'room'])->findOrFail($stayId);
        $this->mostrarCheckoutStay = true;
    }

    /**
     * Realiza check-out de uma estadia direta
     */
    public function realizarCheckoutStay()
    {
        if (!$this->staySelecionada || !$this->staySelecionada->isActive()) {
            session()->flash('error', 'Apenas estadias ativas podem fazer check-out!');
            return;
        }

        if (!$this->tipoPagamento) {
            session()->flash('error', 'Selecione a forma de pagamento!');
            return;
        }

        try {
            $this->checkinService->checkout($this->staySelecionada->id, [
                'payment_type' => $this->tipoPagamento,
            ]);

            session()->flash('success', 'Check-out realizado com sucesso!');
            $this->mostrarCheckoutStay = false;
            $this->staySelecionada = null;
            $this->tipoPagamento = '';
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
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

        // Estadias diretas (walk-in) ativas aguardando check-out
        $staysCheckout = Stay::where('status', 'active')
            ->where('expected_check_out_at', '<=', now())
            ->with(['guest', 'room', 'createdBy'])
            ->orderBy('expected_check_out_at')
            ->get();

        $servicos = ServicoExtra::all();

        // Dados para o modal de check-in direto
        $clientes = Cliente::orderBy('nome')->get();
        
        // Busca quartos disponíveis usando o Service
        $quartosDisponiveis = $this->checkinService 
            ? $this->checkinService->getAvailableRooms()
            : Quarto::whereIn('estado', ['disponivel', 'limpeza'])->orderBy('numero')->get();

        return view('livewire.checkin.index', compact(
            'reservasCheckin', 
            'reservasCheckout', 
            'staysCheckout',
            'servicos', 
            'clientes', 
            'quartosDisponiveis'
        ));
    }
}

