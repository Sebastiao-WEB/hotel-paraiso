<?php

namespace App\Livewire\Limpeza;

use App\Models\Quarto;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $quartoSelecionado = null;
    public $mostrarModal = false;

    public function marcarEmLimpeza($quartoId)
    {
        $quarto = Quarto::findOrFail($quartoId);
        
        if ($quarto->estado !== 'ocupado') {
            session()->flash('error', 'Apenas quartos ocupados podem ser marcados para limpeza!');
            return;
        }

        $quarto->update(['estado' => 'limpeza']);
        session()->flash('success', 'Quarto marcado como "em limpeza"!');
    }

    public function marcarDisponivel($quartoId)
    {
        $quarto = Quarto::findOrFail($quartoId);
        
        if ($quarto->estado !== 'limpeza') {
            session()->flash('error', 'Apenas quartos em limpeza podem ser marcados como disponíveis!');
            return;
        }

        $quarto->update(['estado' => 'disponivel']);
        session()->flash('success', 'Quarto marcado como disponível!');
    }

    public function render()
    {
        $quartosLimpeza = Quarto::where('estado', 'limpeza')->orderBy('numero')->get();
        $quartosOcupados = Quarto::where('estado', 'ocupado')->orderBy('numero')->get();

        return view('livewire.limpeza.index', compact('quartosLimpeza', 'quartosOcupados'));
    }
}


