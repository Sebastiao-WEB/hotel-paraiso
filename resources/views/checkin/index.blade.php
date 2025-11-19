@extends('layouts.dashboard')

@section('title', 'Check-in / Check-out')

@section('content')
<h2 class="mb-4">Check-in / Check-out</h2>

<!-- Check-ins Pendentes -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Check-ins Pendentes</h5>
    </div>
    <div class="card-body">
        @forelse($reservasCheckin as $reserva)
        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-2">
            <div>
                <p class="fw-bold mb-1">{{ $reserva->cliente->nome }}</p>
                <p class="text-muted small mb-0">Quarto {{ $reserva->quarto->numero }} - Entrada: {{ $reserva->data_entrada->format('d/m/Y') }}</p>
            </div>
            <form action="{{ route('admin.checkin.realizar', $reserva->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i> Realizar Check-in
                </button>
            </form>
        </div>
        @empty
        <p class="text-muted text-center py-4">Nenhum check-in pendente</p>
        @endforelse
    </div>
</div>

<!-- Check-outs Pendentes -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Check-outs Pendentes</h5>
    </div>
    <div class="card-body">
        @forelse($reservasCheckout as $reserva)
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="fw-bold mb-1">{{ $reserva->cliente->nome }}</p>
                        <p class="text-muted small mb-1">Quarto {{ $reserva->quarto->numero }} - Saída: {{ $reserva->data_saida->format('d/m/Y') }}</p>
                        <p class="text-primary fw-bold mb-0">Total: MZN {{ number_format($reserva->calcularValorTotal(), 2, ',', '.') }}</p>
                    </div>
                    <div class="col-md-6">
                        <!-- Adicionar Serviços -->
                        <div class="mb-3">
                            <label class="form-label small">Adicionar Serviço:</label>
                            <div class="d-flex gap-2">
                                @foreach($servicos as $servico)
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        data-action="adicionar-servico" 
                                        data-reserva-id="{{ $reserva->id }}" 
                                        data-servico-id="{{ $servico->id }}">
                                    {{ $servico->nome }} (MZN {{ number_format($servico->preco, 2, ',', '.') }})
                                </button>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Serviços Adicionados -->
                        @if($reserva->servicos->count() > 0)
                        <div class="mb-3">
                            <small class="text-muted">Serviços:</small>
                            <ul class="list-unstyled small mb-0">
                                @foreach($reserva->servicos as $rs)
                                <li>{{ $rs->servico->nome }} (x{{ $rs->quantidade }}) - MZN {{ number_format($rs->subtotal, 2, ',', '.') }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Formulário de Check-out -->
                        <form action="{{ route('admin.checkin.checkout', $reserva->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <select name="tipo_pagamento" class="form-select form-select-sm" required>
                                    <option value="">Forma de Pagamento *</option>
                                    <option value="dinheiro">Dinheiro</option>
                                    <option value="cartao">Cartão</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-right me-1"></i> Realizar Check-out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <p class="text-muted text-center py-4">Nenhum check-out pendente</p>
        @endforelse
    </div>
</div>
@endsection


