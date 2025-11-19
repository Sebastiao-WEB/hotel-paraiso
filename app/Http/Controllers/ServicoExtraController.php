<?php

namespace App\Http\Controllers;

use App\Models\ServicoExtra;
use Illuminate\Http\Request;

class ServicoExtraController extends Controller
{
    public function index(Request $request)
    {
        $query = ServicoExtra::query();

        if ($request->filled('search')) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }

        $servicos = $query->orderBy('nome')->paginate(10);

        return view('servicos.index', compact('servicos'));
    }

    public function create()
    {
        return view('servicos.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
        ]);

        ServicoExtra::create($validated);

        return redirect()->route('admin.servicos.index')->with('success', 'Serviço criado com sucesso!');
    }

    public function edit($id)
    {
        $servico = ServicoExtra::findOrFail($id);
        return view('servicos.form', compact('servico'));
    }

    public function update(Request $request, $id)
    {
        $servico = ServicoExtra::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
        ]);

        $servico->update($validated);

        return redirect()->route('admin.servicos.index')->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $servico = ServicoExtra::findOrFail($id);
        $servico->delete();

        return redirect()->route('admin.servicos.index')->with('success', 'Serviço excluído com sucesso!');
    }
}


