<?php

namespace App\Livewire\Checkin;

use App\Models\Reserva;
use App\Models\ServicoExtra;
use App\Models\Cliente;
use App\Models\Quarto;
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
    
    // Propriedades para check-in direto
    public $mostrarCheckinDireto = false;
    public $cliente_id = null;
    public $quarto_id = null;
    public $data_entrada = null;
    public $data_saida = null;

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
     * Abre o modal para check-in direto (sem reserva prévia)
     */
    public function abrirCheckinDireto()
    {
        $this->data_entrada = Carbon::today()->format('Y-m-d');
        $this->data_saida = Carbon::today()->addDay()->format('Y-m-d');
        $this->mostrarCheckinDireto = true;
        $this->reset(['cliente_id', 'quarto_id']);
    }

    /**
     * Realiza check-in direto sem reserva prévia
     */
    public function realizarCheckinDireto()
    {
        // Validação
        $this->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'quarto_id' => 'required|exists:quartos,id',
            'data_entrada' => 'required|date|before_or_equal:today',
            'data_saida' => 'required|date|after:data_entrada',
        ], [
            'cliente_id.required' => 'Selecione um cliente.',
            'quarto_id.required' => 'Selecione um quarto.',
            'data_entrada.required' => 'Informe a data de entrada.',
            'data_entrada.before_or_equal' => 'A data de entrada deve ser hoje ou anterior.',
            'data_saida.required' => 'Informe a data de saída.',
            'data_saida.after' => 'A data de saída deve ser posterior à data de entrada.',
        ]);

        $quarto = Quarto::findOrFail($this->quarto_id);
        $dataEntrada = Carbon::parse($this->data_entrada);
        $dataSaida = Carbon::parse($this->data_saida);

        // Verifica se o quarto está disponível para o período
        if (!$quarto->isDisponivelParaPeriodo($dataEntrada, $dataSaida)) {
            session()->flash('error', 'O quarto selecionado não está disponível para o período informado!');
            return;
        }

        // Verifica se o quarto não está ocupado
        if ($quarto->isOcupado()) {
            session()->flash('error', 'O quarto selecionado está ocupado!');
            return;
        }

        // Calcula o valor total
        $dias = $dataEntrada->diffInDays($dataSaida);
        $valorTotal = $dias * $quarto->preco_diaria;

        // Cria a reserva com status "checkin" diretamente
        $reserva = Reserva::create([
            'cliente_id' => $this->cliente_id,
            'quarto_id' => $this->quarto_id,
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

        session()->flash('success', 'Check-in direto realizado com sucesso!');
        $this->mostrarCheckinDireto = false;
        $this->reset(['cliente_id', 'quarto_id', 'data_entrada', 'data_saida']);
    }

    public function render()
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

        // Dados para o modal de check-in direto
        $clientes = Cliente::orderBy('nome')->get();
        $quartosDisponiveis = Quarto::whereIn('estado', ['disponivel', 'limpeza'])
            ->orderBy('numero')
            ->get();

        return view('livewire.checkin.index', compact('reservasCheckin', 'reservasCheckout', 'servicos', 'clientes', 'quartosDisponiveis'));
    }
}

