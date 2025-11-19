<?php

namespace App\Http\Controllers;

use App\Models\Quarto;
use Illuminate\Http\Request;

class LimpezaController extends Controller
{
    public function index()
    {
        $quartosLimpeza = Quarto::where('estado', 'limpeza')->orderBy('numero')->get();
        $quartosOcupados = Quarto::where('estado', 'ocupado')->orderBy('numero')->get();

        return view('limpeza.index', compact('quartosLimpeza', 'quartosOcupados'));
    }

    public function marcarEmLimpeza($id)
    {
        $quarto = Quarto::findOrFail($id);
        
        if ($quarto->estado !== 'ocupado') {
            return redirect()->route('admin.limpeza.index')->with('error', 'Apenas quartos ocupados podem ser marcados para limpeza!');
        }

        $quarto->update(['estado' => 'limpeza']);

        return redirect()->route('admin.limpeza.index')->with('success', 'Quarto marcado como "em limpeza"!');
    }

    public function marcarDisponivel($id)
    {
        $quarto = Quarto::findOrFail($id);
        
        if ($quarto->estado !== 'limpeza') {
            return redirect()->route('admin.limpeza.index')->with('error', 'Apenas quartos em limpeza podem ser marcados como disponíveis!');
        }

        $quarto->update(['estado' => 'disponivel']);

        return redirect()->route('admin.limpeza.index')->with('success', 'Quarto marcado como disponível!');
    }
}


