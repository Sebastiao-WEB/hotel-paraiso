<?php

namespace App\Livewire\Reservas;

use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Quarto;
use Livewire\Component;
use Carbon\Carbon;

class Form extends Component
{
    public $reservaId;
    public $cliente_id;
    public $quarto_id;
    public $data_entrada;
    public $data_saida;
    public $tipo_pagamento;
    public $quartosDisponiveis = [];

    protected $rules = [
        'cliente_id' => 'required|exists:clientes,id',
        'quarto_id' => 'required|exists:quartos,id',
        'data_entrada' => 'required|date|after_or_equal:today',
        'data_saida' => 'required|date|after:data_entrada',
        'tipo_pagamento' => 'nullable|in:dinheiro,cartao',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $reserva = Reserva::findOrFail($id);
            $this->reservaId = $reserva->id;
            $this->cliente_id = $reserva->cliente_id;
            $this->quarto_id = $reserva->quarto_id;
            $this->data_entrada = $reserva->data_entrada->format('Y-m-d');
            $this->data_saida = $reserva->data_saida->format('Y-m-d');
            $this->tipo_pagamento = $reserva->tipo_pagamento;
        } else {
            $this->data_entrada = Carbon::today()->format('Y-m-d');
            $this->data_saida = Carbon::today()->addDay()->format('Y-m-d');
        }

        $this->atualizarQuartosDisponiveis();
    }

    public function updatedDataEntrada()
    {
        $this->atualizarQuartosDisponiveis();
    }

    public function updatedDataSaida()
    {
        $this->atualizarQuartosDisponiveis();
    }

    public function atualizarQuartosDisponiveis()
    {
        if (!$this->data_entrada || !$this->data_saida) {
            $this->quartosDisponiveis = Quarto::where('estado', 'disponivel')->get();
            return;
        }

        $dataEntrada = Carbon::parse($this->data_entrada);
        $dataSaida = Carbon::parse($this->data_saida);

        // Quartos que não têm reservas conflitantes
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

        $this->quartosDisponiveis = Quarto::where('estado', 'disponivel')
            ->whereNotIn('id', $quartosOcupados)
            ->get();
    }

    public function save()
    {
        $this->validate();

        $quarto = Quarto::findOrFail($this->quarto_id);
        $dias = Carbon::parse($this->data_entrada)->diffInDays(Carbon::parse($this->data_saida));
        $valorTotal = $dias * $quarto->preco_diaria;

        $data = [
            'cliente_id' => $this->cliente_id,
            'quarto_id' => $this->quarto_id,
            'data_entrada' => $this->data_entrada,
            'data_saida' => $this->data_saida,
            'valor_total' => $valorTotal,
            'tipo_pagamento' => $this->tipo_pagamento,
            'status' => 'pendente',
            'criado_por' => auth()->id(),
        ];

        if ($this->reservaId) {
            $reserva = Reserva::findOrFail($this->reservaId);
            $reserva->update($data);
            session()->flash('success', 'Reserva atualizada com sucesso!');
        } else {
            Reserva::create($data);
            session()->flash('success', 'Reserva criada com sucesso!');
        }

        return redirect()->route('reservas.index');
    }

    public function render()
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('livewire.reservas.form', compact('clientes'));
    }
}


