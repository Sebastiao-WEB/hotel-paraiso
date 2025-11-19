<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuartoApiController;
use App\Http\Controllers\Api\ReservaApiController;
use App\Http\Controllers\Api\ServicoApiController;
use App\Http\Controllers\Api\ContatoApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas públicas da API (sem autenticação)
Route::prefix('quartos')->group(function () {
    Route::get('/', [QuartoApiController::class, 'index']);
    Route::get('/disponiveis', [QuartoApiController::class, 'disponiveis']);
    Route::get('/{id}', [QuartoApiController::class, 'show']);
});

Route::prefix('reservas')->group(function () {
    Route::post('/', [ReservaApiController::class, 'store']);
});

Route::prefix('servicos')->group(function () {
    Route::get('/', [ServicoApiController::class, 'index']);
});

Route::prefix('contatos')->group(function () {
    Route::post('/', [ContatoApiController::class, 'store']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
