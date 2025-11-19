<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quarto;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuartoApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Quarto::query();

        if ($request->filled('tipo')) {
            $query->where('tipo', 'like', '%' . $request->tipo . '%');
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $quartos = $query->orderBy('numero')->get();

        return response()->json($quartos);
    }

    public function disponiveis(Request $request)
    {
        $request->validate([
            'data_entrada' => 'required|date',
            'data_saida' => 'required|date|after:data_entrada',
        ]);

        $dataEntrada = Carbon::parse($request->data_entrada);
        $dataSaida = Carbon::parse($request->data_saida);

        // Quartos que não têm reservas conflitantes
        $quartosOcupados = Reserva::where(function($q) use ($dataEntrada, $dataSaida) {
            $q->whereBetween('data_entrada', [$dataEntrada, $dataSaida])
              ->orWhereBetween('data_saida', [$dataEntrada, $dataSaida])
              ->orWhere(function($q2) use ($dataEntrada, $dataSaida) {
                  $q2->where('data_entrada', '<=', $dataEntrada)
                     ->where('data_saida', '>=', $dataSaida);
              });
        })
        ->whereIn('status', ['pendente', 'confirmada', 'checkin'])
        ->pluck('quarto_id');

        $quartos = Quarto::where('estado', 'disponivel')
            ->whereNotIn('id', $quartosOcupados)
            ->get();

        return response()->json($quartos);
    }

    public function show($id)
    {
        $quarto = Quarto::findOrFail($id);
        return response()->json($quarto);
    }
}


