<?php

namespace App\Livewire\Faturacao;

use App\Models\NotaCobranca;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function render()
    {
        $query = NotaCobranca::with(['reserva', 'empresa']);

        if ($this->search) {
            $query->whereHas('empresa', function($q) {
                $q->where('nome', 'like', '%' . $this->search . '%');
            });
        }

        $notas = $query->orderBy('data_emissao', 'desc')->paginate(10);

        return view('livewire.faturacao.index', compact('notas'));
    }
}


