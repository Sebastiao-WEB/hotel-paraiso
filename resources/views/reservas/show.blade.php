@extends('layouts.dashboard')

@section('title', 'Detalhes da Reserva')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalhes da Reserva</h5>
                <a href="{{ route('admin.reservas.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Cliente</h6>
                        <p class="fw-bold">{{ $reserva->cliente->nome }}</p>
                        <p class="text-muted small">{{ $reserva->cliente->tipo === 'empresa' ? 'Empresa' : 'Pessoa Física' }}</p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Quarto</h6>
                        <p class="fw-bold">Quarto {{ $reserva->quarto->numero }}</p>
                        <p class="text-muted small">{{ $reserva->quarto->tipo }}</p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Período</h6>
                        <p class="mb-1"><strong>Entrada:</strong> {{ $reserva->data_entrada->format('d/m/Y') }}</p>
                        <p class="mb-0"><strong>Saída:</strong> {{ $reserva->data_saida->format('d/m/Y') }}</p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Status</h6>
                        <span class="badge 
                            {{ $reserva->status === 'confirmada' ? 'bg-success' : '' }}
                            {{ $reserva->status === 'pendente' ? 'bg-warning' : '' }}
                            {{ $reserva->status === 'checkin' ? 'bg-primary' : '' }}
                            {{ $reserva->status === 'checkout' ? 'bg-secondary' : '' }}
                            {{ $reserva->status === 'cancelada' ? 'bg-danger' : '' }}">
                            {{ ucfirst($reserva->status) }}
                        </span>
                    </div>

                    @if($reserva->servicos->count() > 0)
                    <div class="col-12">
                        <h6 class="text-muted mb-3">Serviços Extras</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Serviço</th>
                                        <th>Quantidade</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reserva->servicos as $rs)
                                    <tr>
                                        <td>{{ $rs->servico->nome }}</td>
                                        <td>{{ $rs->quantidade }}</td>
                                        <td>MZN {{ number_format($rs->subtotal, 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <div class="col-12">
                        <div class="bg-light p-4 rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-5 fw-bold">Valor Total:</span>
                                <span class="fs-4 text-primary fw-bold">MZN {{ number_format($reserva->valor_total, 2, ',', '.') }}</span>
                            </div>
                            @if($reserva->tipo_pagamento)
                            <p class="text-muted small mt-2 mb-0">Forma de Pagamento: {{ ucfirst($reserva->tipo_pagamento) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

