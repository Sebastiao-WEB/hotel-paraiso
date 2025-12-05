@extends('layouts.dashboard')

@section('title', 'Reservas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Gestão de Reservas</h2>
    <a href="{{ route('admin.reservas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Nova Reserva
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reservas.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Buscar por cliente..." class="form-control">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Todos os status</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="confirmada" {{ request('status') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="checkin" {{ request('status') == 'checkin' ? 'selected' : '' }}>Check-in</option>
                        <option value="checkout" {{ request('status') == 'checkout' ? 'selected' : '' }}>Check-out</option>
                        <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="date" name="data" value="{{ request('data') }}" class="form-control">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                    <a href="{{ route('admin.reservas.index') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabela -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Quarto</th>
                        <th>Entrada</th>
                        <th>Saída</th>
                        <th>Status</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservas as $reserva)
                    <tr>
                        <td class="fw-semibold">{{ $reserva->cliente->nome }}</td>
                        <td>Quarto {{ $reserva->quarto->numero }}</td>
                        <td>{{ $reserva->data_entrada->format('d/m/Y') }}</td>
                        <td>{{ $reserva->data_saida->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge 
                                {{ $reserva->status === 'confirmada' ? 'bg-success' : '' }}
                                {{ $reserva->status === 'pendente' ? 'bg-warning' : '' }}
                                {{ $reserva->status === 'checkin' ? 'bg-primary' : '' }}
                                {{ $reserva->status === 'checkout' ? 'bg-secondary' : '' }}
                                {{ $reserva->status === 'cancelada' ? 'bg-danger' : '' }}">
                                {{ ucfirst($reserva->status) }}
                            </span>
                        </td>
                        <td>MZN {{ number_format($reserva->valor_total, 2, ',', '.') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @if($reserva->status === 'pendente')
                                    <form action="{{ route('admin.reservas.confirmar', $reserva->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Confirmar">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($reserva->status === 'checkin')
                                    <button type="button" class="btn btn-outline-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalExtensao{{ $reserva->id }}" 
                                            title="Estender Reserva">
                                        <i class="bi bi-calendar-plus"></i>
                                    </button>
                                @endif
                                @if(!in_array($reserva->status, ['checkout', 'cancelada']))
                                    <form action="{{ route('admin.reservas.cancelar', $reserva->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger" title="Cancelar">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.reservas.show', $reserva->id) }}" class="btn btn-outline-primary" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Nenhuma reserva encontrada</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $reservas->links() }}
    </div>
</div>

<!-- Modais de Extensão -->
@foreach($reservas as $reserva)
@if($reserva->status === 'checkin')
<div class="modal fade" id="modalExtensao{{ $reserva->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.reservas.estender', $reserva->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Estender Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="text-muted mb-2"><strong>Cliente:</strong> {{ $reserva->cliente->nome }}</p>
                        <p class="text-muted mb-2"><strong>Quarto:</strong> {{ $reserva->quarto->numero }}</p>
                        <p class="text-muted mb-2"><strong>Data de Saída Atual:</strong> {{ $reserva->data_saida->format('d/m/Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <label for="dias_adicionais{{ $reserva->id }}" class="form-label">Dias Adicionais *</label>
                        <input type="number" 
                               name="dias_adicionais" 
                               id="dias_adicionais{{ $reserva->id }}"
                               class="form-control" 
                               min="1" 
                               max="30" 
                               value="1" 
                               required>
                        <small class="text-muted">Máximo de 30 dias</small>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <strong>Nota:</strong> O sistema verificará a disponibilidade do quarto antes de estender a reserva.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Confirmar Extensão</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection

