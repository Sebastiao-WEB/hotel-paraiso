<?php

namespace App\Livewire\Servicos;

use App\Models\ServicoExtra;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function delete($id)
    {
        $servico = ServicoExtra::findOrFail($id);
        $servico->delete();
        
        session()->flash('success', 'Serviço excluído com sucesso!');
    }

    public function render()
    {
        $query = ServicoExtra::query();

        if ($this->search) {
            $query->where('nome', 'like', '%' . $this->search . '%');
        }

        $servicos = $query->orderBy('nome')->paginate(10);

        return view('livewire.servicos.index', compact('servicos'));
    }
}


