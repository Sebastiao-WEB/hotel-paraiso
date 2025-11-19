<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;

class Form extends Component
{
    public $clienteId;
    public $nome;
    public $tipo = 'pessoa';
    public $email;
    public $telefone;
    public $nif;
    public $endereco;

    protected $rules = [
        'nome' => 'required|string|max:255',
        'tipo' => 'required|in:pessoa,empresa',
        'email' => 'nullable|email|max:255',
        'telefone' => 'nullable|string|max:20',
        'nif' => 'nullable|string|max:20',
        'endereco' => 'nullable|string',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $cliente = Cliente::findOrFail($id);
            $this->clienteId = $cliente->id;
            $this->nome = $cliente->nome;
            $this->tipo = $cliente->tipo;
            $this->email = $cliente->email;
            $this->telefone = $cliente->telefone;
            $this->nif = $cliente->nif;
            $this->endereco = $cliente->endereco;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nome' => $this->nome,
            'tipo' => $this->tipo,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'nif' => $this->nif,
            'endereco' => $this->endereco,
        ];

        if ($this->clienteId) {
            $cliente = Cliente::findOrFail($this->clienteId);
            $cliente->update($data);
            session()->flash('success', 'Cliente atualizado com sucesso!');
        } else {
            Cliente::create($data);
            session()->flash('success', 'Cliente criado com sucesso!');
        }

        return redirect()->route('clientes.index');
    }

    public function render()
    {
        return view('livewire.clientes.form');
    }
}


