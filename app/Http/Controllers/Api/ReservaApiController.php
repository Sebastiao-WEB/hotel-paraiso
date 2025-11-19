<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Quarto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReservaApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quarto_id' => 'required|exists:quartos,id',
            'data_entrada' => 'required|date|after_or_equal:today',
            'data_saida' => 'required|date|after:data_entrada',
            'nome_cliente' => 'required|string|max:255',
            'email_cliente' => 'required|email|max:255',
            'telefone_cliente' => 'required|string|max:20',
            'tipo_pagamento' => 'nullable|in:dinheiro,cartao',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Criar ou buscar cliente
            $cliente = Cliente::firstOrCreate(
                ['email' => $request->email_cliente],
                [
                    'nome' => $request->nome_cliente,
                    'telefone' => $request->telefone_cliente,
                    'tipo' => 'pessoa',
                ]
            );

            // Atualizar dados do cliente se necessário
            if ($cliente->nome !== $request->nome_cliente || $cliente->telefone !== $request->telefone_cliente) {
                $cliente->update([
                    'nome' => $request->nome_cliente,
                    'telefone' => $request->telefone_cliente,
                ]);
            }

            $quarto = Quarto::findOrFail($request->quarto_id);
            $dias = Carbon::parse($request->data_entrada)->diffInDays(Carbon::parse($request->data_saida));
            $valorTotal = $dias * $quarto->preco_diaria;

            $reserva = Reserva::create([
                'cliente_id' => $cliente->id,
                'quarto_id' => $request->quarto_id,
                'data_entrada' => $request->data_entrada,
                'data_saida' => $request->data_saida,
                'valor_total' => $valorTotal,
                'tipo_pagamento' => $request->tipo_pagamento,
                'status' => 'pendente',
                'criado_por' => null, // Reserva pública
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva criada com sucesso!',
                'data' => $reserva
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar reserva: ' . $e->getMessage()
            ], 500);
        }
    }
}


