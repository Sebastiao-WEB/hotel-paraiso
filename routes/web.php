<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\QuartoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\ServicoExtraController;
use App\Http\Controllers\FaturacaoController;
use App\Http\Controllers\LimpezaController;
use App\Http\Controllers\NotaCobrancaController;
use App\Http\Controllers\UserController;

// Rotas Públicas
Route::get('/', [App\Http\Controllers\Public\HomeController::class, 'index'])->name('public.home');
Route::get('/quartos', [App\Http\Controllers\Public\RoomsController::class, 'index'])->name('public.rooms');
Route::get('/quartos/{id}', [App\Http\Controllers\Public\RoomsController::class, 'show'])->name('public.room-detail');
Route::get('/servicos', [App\Http\Controllers\Public\ServicesController::class, 'index'])->name('public.services');
Route::get('/sobre', [App\Http\Controllers\Public\AboutController::class, 'index'])->name('public.about');
Route::get('/contato', [App\Http\Controllers\Public\ContactController::class, 'index'])->name('public.contact');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Clientes
    Route::resource('clientes', ClienteController::class)->name('get', 'clientes');
    
    // Quartos
    Route::resource('quartos', QuartoController::class);
    Route::post('/quartos/{id}/alterar-estado', [QuartoController::class, 'alterarEstado'])->name('quartos.alterar-estado');
    
    // Reservas
    Route::resource('reservas', ReservaController::class);
    Route::post('/reservas/{id}/confirmar', [ReservaController::class, 'confirmar'])->name('reservas.confirmar');
    Route::post('/reservas/{id}/cancelar', [ReservaController::class, 'cancelar'])->name('reservas.cancelar');
    Route::get('/reservas/quartos-disponiveis', [ReservaController::class, 'getQuartosDisponiveis'])->name('reservas.quartos-disponiveis');
    
    // Check-in/Check-out
    Route::get('/checkin', [CheckinController::class, 'index'])->name('checkin.index');
    Route::post('/checkin/{id}/realizar-checkin', [CheckinController::class, 'realizarCheckin'])->name('checkin.realizar');
    Route::post('/checkin/{id}/realizar-checkout', [CheckinController::class, 'realizarCheckout'])->name('checkin.checkout');
    Route::post('/checkin/{id}/adicionar-servico', [CheckinController::class, 'adicionarServico'])->name('checkin.adicionar-servico');
    
    // Serviços Extras
    Route::resource('servicos', ServicoExtraController::class);
    
    // Faturação
    Route::get('/faturacao', [FaturacaoController::class, 'index'])->name('faturacao.index');
    Route::get('/faturacao/{id}/pdf', [NotaCobrancaController::class, 'pdf'])->name('faturacao.pdf');
    
    // Limpeza
    Route::get('/limpeza', [LimpezaController::class, 'index'])->name('limpeza.index');
    Route::post('/limpeza/{id}/marcar-limpeza', [LimpezaController::class, 'marcarEmLimpeza'])->name('limpeza.marcar-limpeza');
    Route::post('/limpeza/{id}/marcar-disponivel', [LimpezaController::class, 'marcarDisponivel'])->name('limpeza.marcar-disponivel');
    
    // Usuários
    Route::resource('users', UserController::class);
});

// Redirecionar /dashboard para /admin/dashboard para manter compatibilidade
Route::middleware(['auth'])->get('/dashboard', function() {
    return redirect()->route('admin.dashboard');
});

require __DIR__.'/auth.php';
