@extends('layouts.dashboard')

@section('title', 'Quartos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Gestão de Quartos</h2>
    <a href="{{ route('admin.quartos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Novo Quarto
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.quartos.index') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Buscar por número ou tipo..." class="form-control">
                </div>
                <div class="col-md-6">
                    <select name="estado" class="form-select">
                        <option value="">Todos os estados</option>
                        <option value="disponivel" {{ request('estado') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                        <option value="reservado" {{ request('estado') == 'reservado' ? 'selected' : '' }}>Reservado</option>
                        <option value="ocupado" {{ request('estado') == 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                        <option value="limpeza" {{ request('estado') == 'limpeza' ? 'selected' : '' }}>Em Limpeza</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                    <a href="{{ route('admin.quartos.index') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Grid de Quartos -->
<div class="row g-3">
    @forelse($quartos as $quarto)
    @php
        // Buscar ocupação atual
        $reservaAtiva = \App\Models\Reserva::where('quarto_id', $quarto->id)
            ->where('status', 'checkin')
            ->where('data_saida', '>=', \Carbon\Carbon::today())
            ->with('cliente')
            ->first();
        
        $stayAtiva = \App\Models\Stay::where('room_id', $quarto->id)
            ->where('status', 'active')
            ->with('guest')
            ->first();
    @endphp
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card h-100 border-start border-4 
            {{ $quarto->estado === 'disponivel' ? 'border-success' : '' }}
            {{ $quarto->estado === 'reservado' ? 'border-warning' : '' }}
            {{ $quarto->estado === 'ocupado' ? 'border-danger' : '' }}
            {{ $quarto->estado === 'limpeza' ? 'border-info' : '' }}
            cursor-pointer" 
            style="cursor: pointer;"
            onclick="window.location.href='{{ route('admin.quartos.show', $quarto->id) }}'"
            onmouseover="this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'"
            onmouseout="this.style.boxShadow='none'">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">
                            <a href="{{ route('admin.quartos.show', $quarto->id) }}" class="text-decoration-none text-dark">
                                Quarto {{ $quarto->numero }}
                            </a>
                        </h5>
                        <p class="text-muted small mb-0">{{ $quarto->tipo }}</p>
                    </div>
                    <span class="badge 
                        {{ $quarto->estado === 'disponivel' ? 'bg-success' : '' }}
                        {{ $quarto->estado === 'reservado' ? 'bg-warning' : '' }}
                        {{ $quarto->estado === 'ocupado' ? 'bg-danger' : '' }}
                        {{ $quarto->estado === 'limpeza' ? 'bg-info' : '' }}">
                        {{ ucfirst($quarto->estado) }}
                    </span>
                </div>
                <p class="mb-2"><strong>MZN {{ number_format($quarto->preco_diaria, 2, ',', '.') }}/dia</strong></p>
                
                @if($quarto->estado === 'ocupado')
                    @if($reservaAtiva)
                    <div class="alert alert-danger py-2 px-2 mb-2" style="font-size: 0.85rem;">
                        <strong><i class="bi bi-person-fill me-1"></i>{{ $reservaAtiva->cliente->nome }}</strong><br>
                        <small>Saída: {{ $reservaAtiva->data_saida->format('d/m/Y') }}</small>
                    </div>
                    @elseif($stayAtiva)
                    <div class="alert alert-warning py-2 px-2 mb-2" style="font-size: 0.85rem;">
                        <strong><i class="bi bi-person-fill me-1"></i>{{ $stayAtiva->guest->nome }}</strong>
                        <span class="badge bg-warning text-dark ms-1">WALK-IN</span><br>
                        <small>Saída: {{ $stayAtiva->expected_check_out_at->format('d/m/Y H:i') }}</small>
                    </div>
                    @endif
                @elseif($quarto->estado === 'reservado')
                    @php
                        $reservaConfirmada = \App\Models\Reserva::where('quarto_id', $quarto->id)
                            ->where('status', 'confirmada')
                            ->where('data_entrada', '>=', \Carbon\Carbon::today())
                            ->with('cliente')
                            ->orderBy('data_entrada')
                            ->first();
                    @endphp
                    @if($reservaConfirmada)
                    <div class="alert alert-warning py-2 px-2 mb-2" style="font-size: 0.85rem;">
                        <strong><i class="bi bi-calendar-check me-1"></i>Reservado</strong><br>
                        <small>Entrada: {{ $reservaConfirmada->data_entrada->format('d/m/Y') }}</small>
                    </div>
                    @endif
                @endif
                
                <div class="d-grid gap-2" onclick="event.stopPropagation();">
                    <a href="{{ route('admin.quartos.show', $quarto->id) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye me-1"></i> Ver Detalhes
                    </a>
                    <a href="{{ route('admin.quartos.edit', $quarto->id) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    <select data-action="alterar-estado-quarto" data-quarto-id="{{ $quarto->id }}" 
                            class="form-select form-select-sm" onclick="event.stopPropagation();">
                        <option value="disponivel" {{ $quarto->estado === 'disponivel' ? 'selected' : '' }}>Disponível</option>
                        <option value="reservado" {{ $quarto->estado === 'reservado' ? 'selected' : '' }}>Reservado</option>
                        <option value="ocupado" {{ $quarto->estado === 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                        <option value="limpeza" {{ $quarto->estado === 'limpeza' ? 'selected' : '' }}>Limpeza</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center">Nenhum quarto encontrado</div>
    </div>
    @endforelse
</div>

<!-- Paginação -->
<div class="mt-4">
    {{ $quartos->links() }}
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Alterar estado do quarto
    document.querySelectorAll('[data-action="alterar-estado-quarto"]').forEach(function(select) {
        select.addEventListener('change', function() {
            const quartoId = this.getAttribute('data-quarto-id');
            const novoEstado = this.value;
            
            if (confirm('Deseja realmente alterar o estado deste quarto?')) {
                fetch(`/admin/quartos/${quartoId}/alterar-estado`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ estado: novoEstado })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao alterar estado do quarto');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao alterar estado do quarto');
                    location.reload();
                });
            } else {
                location.reload();
            }
        });
    });
});
</script>
@endsection

