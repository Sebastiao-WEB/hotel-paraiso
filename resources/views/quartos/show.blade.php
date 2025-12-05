@extends('layouts.dashboard')

@section('title', 'Detalhes do Quarto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Quarto {{ $quarto->numero }}</h2>
        <p class="text-muted mb-0">{{ $quarto->tipo }} - MZN {{ number_format($quarto->preco_diaria, 2, ',', '.') }}/dia</p>
    </div>
    <div>
        <a href="{{ route('admin.quartos.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
        <a href="{{ route('admin.quartos.edit', $quarto->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Editar
        </a>
    </div>
</div>

<div class="row">
    <!-- Informações do Quarto -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações do Quarto</h5>
            </div>
            <div class="card-body">
                <p><strong>Número:</strong> {{ $quarto->numero }}</p>
                <p><strong>Tipo:</strong> {{ $quarto->tipo }}</p>
                <p><strong>Preço Diária:</strong> MZN {{ number_format($quarto->preco_diaria, 2, ',', '.') }}</p>
                <p>
                    <strong>Estado:</strong> 
                    <span class="badge 
                        {{ $quarto->estado === 'disponivel' ? 'bg-success' : '' }}
                        {{ $quarto->estado === 'reservado' ? 'bg-warning' : '' }}
                        {{ $quarto->estado === 'ocupado' ? 'bg-danger' : '' }}
                        {{ $quarto->estado === 'limpeza' ? 'bg-info' : '' }}">
                        {{ ucfirst($quarto->estado) }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Ocupação Atual -->
    <div class="col-md-8">
        @if($quarto->estado === 'ocupado')
            @if($reservaAtiva)
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger bg-opacity-10">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-fill me-2"></i>Ocupado por Reserva
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> {{ $reservaAtiva->cliente->nome }}</p>
                            <p><strong>Entrada:</strong> {{ $reservaAtiva->data_entrada->format('d/m/Y') }}</p>
                            <p><strong>Saída Prevista:</strong> {{ $reservaAtiva->data_saida->format('d/m/Y') }}</p>
                            <p><strong>Check-in em:</strong> {{ $reservaAtiva->checkin_em ? $reservaAtiva->checkin_em->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Valor Total:</strong> MZN {{ number_format($reservaAtiva->calcularValorTotal(), 2, ',', '.') }}</p>
                            @if($reservaAtiva->servicos->count() > 0)
                            <p><strong>Serviços Extras:</strong></p>
                            <ul class="list-unstyled">
                                @foreach($reservaAtiva->servicos as $servico)
                                <li>- {{ $servico->servico->nome }} (x{{ $servico->quantidade }}) - MZN {{ number_format($servico->subtotal, 2, ',', '.') }}</li>
                                @endforeach
                            </ul>
                            @endif
                            <a href="{{ route('admin.checkin.index') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-box-arrow-right me-1"></i> Ver Check-out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @elseif($stayAtiva)
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-fill me-2"></i>Ocupado por Walk-in
                        <span class="badge bg-warning text-dark ms-2">WALK-IN</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Hóspede:</strong> {{ $stayAtiva->guest->nome }}</p>
                            <p><strong>Entrada:</strong> {{ $stayAtiva->check_in_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Saída Prevista:</strong> {{ $stayAtiva->expected_check_out_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Noites:</strong> {{ $stayAtiva->nights }} noite(s)</p>
                            <p><strong>Registrado por:</strong> {{ $stayAtiva->createdBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Valor Estimado:</strong> MZN {{ number_format($stayAtiva->calculateTotalAmount(), 2, ',', '.') }}</p>
                            @if($stayAtiva->notes)
                            <p><strong>Observações:</strong></p>
                            <p class="text-muted">{{ $stayAtiva->notes }}</p>
                            @endif
                            <a href="{{ route('admin.checkin.index') }}" class="btn btn-sm btn-warning mt-2">
                                <i class="bi bi-box-arrow-right me-1"></i> Ver Check-out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @elseif($quarto->estado === 'reservado')
            @if($reservasFuturas->count() > 0)
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-check me-2"></i>Próximas Reservas
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($reservasFuturas as $reserva)
                    <div class="border-bottom pb-3 mb-3">
                        <p><strong>Cliente:</strong> {{ $reserva->cliente->nome }}</p>
                        <p><strong>Entrada:</strong> {{ $reserva->data_entrada->format('d/m/Y') }}</p>
                        <p><strong>Saída:</strong> {{ $reserva->data_saida->format('d/m/Y') }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-warning">{{ ucfirst($reserva->status) }}</span>
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @else
        <div class="card mb-4 border-success">
            <div class="card-header bg-success bg-opacity-10">
                <h5 class="card-title mb-0">
                    <i class="bi bi-check-circle me-2"></i>Quarto Disponível
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">O quarto está disponível para reserva ou check-in direto.</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Histórico de Ocupação -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Histórico de Ocupação</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="historicoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="reservas-tab" data-bs-toggle="tab" data-bs-target="#reservas" type="button" role="tab">
                            Reservas ({{ $historicoReservas->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="stays-tab" data-bs-toggle="tab" data-bs-target="#stays" type="button" role="tab">
                            Walk-ins ({{ $historicoStays->count() }})
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3" id="historicoTabsContent">
                    <div class="tab-pane fade show active" id="reservas" role="tabpanel">
                        @if($historicoReservas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Entrada</th>
                                        <th>Saída</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historicoReservas as $reserva)
                                    <tr>
                                        <td>{{ $reserva->cliente->nome }}</td>
                                        <td>{{ $reserva->data_entrada->format('d/m/Y') }}</td>
                                        <td>{{ $reserva->data_saida->format('d/m/Y') }}</td>
                                        <td>MZN {{ number_format($reserva->valor_total, 2, ',', '.') }}</td>
                                        <td>
                                            <span class="badge 
                                                {{ $reserva->status === 'checkout' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($reserva->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted text-center py-4">Nenhuma reserva no histórico</p>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="stays" role="tabpanel">
                        @if($historicoStays->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Hóspede</th>
                                        <th>Entrada</th>
                                        <th>Saída</th>
                                        <th>Noites</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historicoStays as $stay)
                                    <tr>
                                        <td>
                                            {{ $stay->guest->nome }}
                                            <span class="badge bg-warning text-dark ms-1">WALK-IN</span>
                                        </td>
                                        <td>{{ $stay->check_in_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $stay->actual_check_out_at ? $stay->actual_check_out_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>{{ $stay->nights }}</td>
                                        <td>MZN {{ number_format($stay->total_amount, 2, ',', '.') }}</td>
                                        <td>
                                            <span class="badge 
                                                {{ $stay->status === 'completed' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($stay->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted text-center py-4">Nenhuma estadia walk-in no histórico</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

