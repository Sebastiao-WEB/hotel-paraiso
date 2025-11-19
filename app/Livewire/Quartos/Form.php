<?php

namespace App\Livewire\Quartos;

use App\Models\Quarto;
use Livewire\Component;

class Form extends Component
{
    public $quartoId;
    public $numero;
    public $tipo;
    public $preco_diaria;
    public $estado = 'disponivel';

    protected $rules = [
        'numero' => 'required|string|max:10|unique:quartos,numero',
        'tipo' => 'required|string|max:255',
        'preco_diaria' => 'required|numeric|min:0',
        'estado' => 'required|in:disponivel,reservado,ocupado,limpeza',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $quarto = Quarto::findOrFail($id);
            $this->quartoId = $quarto->id;
            $this->numero = $quarto->numero;
            $this->tipo = $quarto->tipo;
            $this->preco_diaria = $quarto->preco_diaria;
            $this->estado = $quarto->estado;
            
            $this->rules['numero'] = 'required|string|max:10|unique:quartos,numero,' . $quarto->id;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'numero' => $this->numero,
            'tipo' => $this->tipo,
            'preco_diaria' => $this->preco_diaria,
            'estado' => $this->estado,
        ];

        if ($this->quartoId) {
            $quarto = Quarto::findOrFail($this->quartoId);
            $quarto->update($data);
            session()->flash('success', 'Quarto atualizado com sucesso!');
        } else {
            Quarto::create($data);
            session()->flash('success', 'Quarto criado com sucesso!');
        }

        return redirect()->route('quartos.index');
    }

    public function render()
    {
        return view('livewire.quartos.form');
    }
}


