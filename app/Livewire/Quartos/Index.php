<?php

namespace App\Livewire\Quartos;

use App\Models\Quarto;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $estadoFiltro = '';

    protected $queryString = ['search', 'estadoFiltro'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $quarto = Quarto::findOrFail($id);
        $quarto->delete();
        
        session()->flash('success', 'Quarto excluído com sucesso!');
    }

    public function alterarEstado($id, $novoEstado)
    {
        $quarto = Quarto::findOrFail($id);
        $quarto->update(['estado' => $novoEstado]);
        
        session()->flash('success', 'Estado do quarto atualizado com sucesso!');
    }

    public function render()
    {
        $query = Quarto::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('numero', 'like', '%' . $this->search . '%')
                  ->orWhere('tipo', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->estadoFiltro) {
            $query->where('estado', $this->estadoFiltro);
        }

        $quartos = $query->orderBy('numero')->paginate(10);

        return view('livewire.quartos.index', compact('quartos'));
    }
}


