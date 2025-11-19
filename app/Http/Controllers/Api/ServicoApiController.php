<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServicoExtra;
use Illuminate\Http\Request;

class ServicoApiController extends Controller
{
    public function index()
    {
        $servicos = ServicoExtra::orderBy('nome')->get();
        return response()->json($servicos);
    }
}


