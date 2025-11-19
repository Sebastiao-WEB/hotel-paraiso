<?php

namespace App\Http\Controllers;

use App\Models\NotaCobranca;
use Illuminate\Http\Request;

class FaturacaoController extends Controller
{
    public function index(Request $request)
    {
        $query = NotaCobranca::with(['reserva', 'empresa']);

        if ($request->filled('search')) {
            $query->whereHas('empresa', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->search . '%');
            });
        }

        $notas = $query->orderBy('data_emissao', 'desc')->paginate(10);

        return view('faturacao.index', compact('notas'));
    }
}


