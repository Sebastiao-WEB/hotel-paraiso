<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('telefone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $clientes = $query->orderBy('nome')->paginate(10);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:pessoa,empresa',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'nif' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
        ]);

        Cliente::create($validated);

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente criado com sucesso!');
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.form', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:pessoa,empresa',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'nif' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
        ]);

        $cliente->update($validated);

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.show', compact('cliente'));
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente excluído com sucesso!');
    }
}

