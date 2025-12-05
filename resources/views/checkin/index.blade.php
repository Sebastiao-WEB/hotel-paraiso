@extends('layouts.dashboard')

@section('title', 'Check-in / Check-out')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Check-in / Check-out</h2>
    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCheckinDireto">
        <i class="bi bi-plus-circle me-1"></i>
        Check-in Direto (Walk-in)
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

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

<!-- Check-outs Pendentes (Estadias Diretas - Walk-in) -->
@if(isset($staysCheckout) && $staysCheckout->count() > 0)
<div class="card mt-4 border-warning">
    <div class="card-header bg-warning bg-opacity-10">
        <h5 class="card-title mb-0">
            Check-outs Pendentes (Walk-in)
            <span class="badge bg-warning text-dark ms-2">{{ $staysCheckout->count() }}</span>
        </h5>
    </div>
    <div class="card-body">
        @foreach($staysCheckout as $stay)
        <div class="card mb-3 border-warning">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="fw-bold mb-1">
                            {{ $stay->guest->nome }}
                            <span class="badge bg-warning text-dark ms-2">WALK-IN</span>
                        </p>
                        <p class="text-muted small mb-1">
                            Quarto {{ $stay->room->numero }} - 
                            Entrada: {{ $stay->check_in_at->format('d/m/Y H:i') }} - 
                            Saída Prevista: {{ $stay->expected_check_out_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="text-primary fw-bold mb-0">
                            Total Estimado: MZN {{ number_format($stay->calculateTotalAmount(), 2, ',', '.') }}
                        </p>
                        <small class="text-muted">
                            Registrado por: {{ $stay->createdBy->name ?? 'N/A' }} em {{ $stay->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('admin.checkin.stay.checkout', $stay->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <select name="payment_type" class="form-select form-select-sm" required>
                                    <option value="">Forma de Pagamento *</option>
                                    <option value="dinheiro">Dinheiro</option>
                                    <option value="cartao">Cartão</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-box-arrow-right me-1"></i> Realizar Check-out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Modal Check-in Direto -->
<div class="modal fade" id="modalCheckinDireto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.checkin.direto') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Check-in Direto (Walk-in)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="guest_id" class="form-label">Cliente *</label>
                        <select name="guest_id" id="guest_id" class="form-select" required>
                            <option value="">Selecione um cliente...</option>
                            @foreach(\App\Models\Cliente::orderBy('nome')->get() as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="room_id" class="form-label">Quarto *</label>
                        <select name="room_id" id="room_id" class="form-select" required>
                            <option value="">Selecione um quarto...</option>
                            @foreach(\App\Models\Quarto::whereIn('estado', ['disponivel', 'limpeza'])->orderBy('numero')->get() as $quarto)
                            <option value="{{ $quarto->id }}">
                                Quarto {{ $quarto->numero }} - {{ $quarto->tipo }} 
                                (MZN {{ number_format($quarto->preco_diaria, 2, ',', '.') }}/dia)
                                @if($quarto->estado === 'limpeza')
                                    - Em Limpeza
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="check_in_at" class="form-label">Data e Hora de Entrada</label>
                        <input type="datetime-local" 
                               name="check_in_at" 
                               id="check_in_at" 
                               class="form-control"
                               max="{{ now()->format('Y-m-d\TH:i') }}">
                        <small class="text-muted">Deixe em branco para usar o momento atual</small>
                    </div>

                    <div class="mb-3">
                        <label for="expected_check_out_at" class="form-label">Data e Hora Prevista de Saída *</label>
                        <input type="datetime-local" 
                               name="expected_check_out_at" 
                               id="expected_check_out_at" 
                               class="form-control"
                               min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Observações adicionais sobre a estadia..."></textarea>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <strong>Nota:</strong> O sistema verificará a disponibilidade do quarto antes de realizar o check-in.
                            O valor final será calculado no check-out baseado no tempo real de permanência.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle me-1"></i> Confirmar Check-in Direto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


