<?php

namespace App\Livewire\Servicos;

use App\Models\ServicoExtra;
use Livewire\Component;

class Form extends Component
{
    public $servicoId;
    public $nome;
    public $preco;

    protected $rules = [
        'nome' => 'required|string|max:255',
        'preco' => 'required|numeric|min:0',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $servico = ServicoExtra::findOrFail($id);
            $this->servicoId = $servico->id;
            $this->nome = $servico->nome;
            $this->preco = $servico->preco;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nome' => $this->nome,
            'preco' => $this->preco,
        ];

        if ($this->servicoId) {
            $servico = ServicoExtra::findOrFail($this->servicoId);
            $servico->update($data);
            session()->flash('success', 'Serviço atualizado com sucesso!');
        } else {
            ServicoExtra::create($data);
            session()->flash('success', 'Serviço criado com sucesso!');
        }

        return redirect()->route('servicos.index');
    }

    public function render()
    {
        return view('livewire.servicos.form');
    }
}


