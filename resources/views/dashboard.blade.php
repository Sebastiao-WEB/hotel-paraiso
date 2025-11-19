@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stats Cards -->
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Clientes</p>
                        <h3 class="mb-0">{{ $stats['total_clientes'] }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-people fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Quartos Disponíveis</p>
                        <h3 class="mb-0">{{ $stats['quartos_disponiveis'] }} / {{ $stats['total_quartos'] }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-door-open fs-4 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Reservas Ativas</p>
                        <h3 class="mb-0">{{ $stats['reservas_ativas'] }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-calendar-check fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Receita do Mês</p>
                        <h3 class="mb-0">MZN {{ number_format($stats['receita_mes'], 2, ',', '.') }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-currency-dollar fs-4 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Lists -->
<div class="row g-4 mb-4">
    <!-- Ocupação Diária -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ocupação (Últimos 7 dias)</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-end justify-content-between" style="height: 250px;">
                    @foreach($ocupacao_diaria as $dia)
                    <div class="flex-fill d-flex flex-column align-items-center me-2">
                        <div class="w-100 bg-light rounded-top" style="height: {{ max(10, ($dia['ocupados'] / max(1, $stats['total_quartos'])) * 100) }}%">
                            <div class="bg-primary w-100 rounded-top" style="height: 100%"></div>
                        </div>
                        <small class="text-muted mt-2">{{ $dia['data'] }}</small>
                        <small class="fw-bold">{{ $dia['ocupados'] }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Próximos Check-ins -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Próximos Check-ins</h5>
            </div>
            <div class="card-body">
                @forelse($proximos_checkins as $reserva)
                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-2">
                    <div>
                        <p class="fw-bold mb-1">{{ $reserva->cliente->nome }}</p>
                        <p class="text-muted small mb-0">Quarto {{ $reserva->quarto->numero }} - {{ $reserva->data_entrada->format('d/m/Y') }}</p>
                    </div>
                    <span class="badge bg-primary">{{ $reserva->data_entrada->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-muted text-center py-4">Nenhum check-in agendado</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Próximos Check-outs -->
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Próximos Check-outs</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($proximos_checkouts as $reserva)
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <p class="fw-bold mb-1">{{ $reserva->cliente->nome }}</p>
                            <p class="text-muted small mb-1">Quarto {{ $reserva->quarto->numero }}</p>
                            <p class="text-muted small mb-1">Saída: {{ $reserva->data_saida->format('d/m/Y') }}</p>
                            <p class="text-primary fw-bold mb-0">MZN {{ number_format($reserva->valor_total, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-muted text-center py-4">Nenhum check-out agendado</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
