<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContatoApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'assunto' => 'required|string|max:255',
            'mensagem' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Aqui você pode salvar em uma tabela de contatos ou enviar email
            // Por enquanto, apenas retornamos sucesso
            
            // Exemplo de como salvar (se tiver tabela de contatos):
            // Contato::create($request->all());
            
            // Exemplo de envio de email:
            // Mail::to('info@hotelparaiso.co.mz')->send(new ContatoMail($request->all()));

            return response()->json([
                'success' => true,
                'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar mensagem: ' . $e->getMessage()
            ], 500);
        }
    }
}


