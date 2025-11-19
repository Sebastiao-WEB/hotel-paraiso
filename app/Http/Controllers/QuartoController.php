<?php

namespace App\Http\Controllers;

use App\Models\Quarto;
use Illuminate\Http\Request;

class QuartoController extends Controller
{
    public function index(Request $request)
    {
        $query = Quarto::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', '%' . $search . '%')
                  ->orWhere('tipo', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $quartos = $query->orderBy('numero')->paginate(10);

        return view('quartos.index', compact('quartos'));
    }

    public function create()
    {
        return view('quartos.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:10|unique:quartos,numero',
            'tipo' => 'required|string|max:255',
            'preco_diaria' => 'required|numeric|min:0',
            'estado' => 'required|in:disponivel,reservado,ocupado,limpeza',
        ]);

        Quarto::create($validated);

        return redirect()->route('quartos.index')->with('success', 'Quarto criado com sucesso!');
    }

    public function edit($id)
    {
        $quarto = Quarto::findOrFail($id);
        return view('quartos.form', compact('quarto'));
    }

    public function update(Request $request, $id)
    {
        $quarto = Quarto::findOrFail($id);

        $validated = $request->validate([
            'numero' => 'required|string|max:10|unique:quartos,numero,' . $quarto->id,
            'tipo' => 'required|string|max:255',
            'preco_diaria' => 'required|numeric|min:0',
            'estado' => 'required|in:disponivel,reservado,ocupado,limpeza',
        ]);

        $quarto->update($validated);

        return redirect()->route('admin.quartos.index')->with('success', 'Quarto atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $quarto = Quarto::findOrFail($id);
        $quarto->delete();

        return redirect()->route('admin.quartos.index')->with('success', 'Quarto excluído com sucesso!');
    }

    public function show($id)
    {
        $quarto = Quarto::findOrFail($id);
        return view('quartos.show', compact('quarto'));
    }

    public function alterarEstado(Request $request, $id)
    {
        $quarto = Quarto::findOrFail($id);
        $quarto->update(['estado' => $request->estado]);

        return response()->json(['success' => true, 'message' => 'Estado atualizado com sucesso!']);
    }
}

