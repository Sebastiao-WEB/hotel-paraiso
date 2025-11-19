<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $tipoFiltro = '';

    protected $queryString = ['search', 'tipoFiltro'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();
        
        session()->flash('success', 'Cliente excluído com sucesso!');
    }

    public function render()
    {
        $query = Cliente::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nome', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('telefone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->tipoFiltro) {
            $query->where('tipo', $this->tipoFiltro);
        }

        $clientes = $query->orderBy('nome')->paginate(10);

        return view('livewire.clientes.index', compact('clientes'));
    }
}


