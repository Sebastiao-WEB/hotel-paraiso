<?php

namespace App\Livewire\Checkin;

use App\Models\Reserva;
use App\Models\ServicoExtra;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $reservaSelecionada = null;
    public $mostrarCheckin = false;
    public $mostrarCheckout = false;
    public $servicosSelecionados = [];
    public $tipoPagamento = '';

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

    public function render()
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

        return view('livewire.checkin.index', compact('reservasCheckin', 'reservasCheckout', 'servicos'));
    }
}

