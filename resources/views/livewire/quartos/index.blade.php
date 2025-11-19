@extends('layouts.dashboard')

@section('title', 'Quartos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Gestão de Quartos</h2>
    <a href="{{ route('quartos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Novo Quarto
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" wire:model.live="search" placeholder="Buscar por número ou tipo..." 
                       class="form-control">
            </div>
            <div class="col-md-6">
                <select wire:model.live="estadoFiltro" class="form-select">
                    <option value="">Todos os estados</option>
                    <option value="disponivel">Disponível</option>
                    <option value="reservado">Reservado</option>
                    <option value="ocupado">Ocupado</option>
                    <option value="limpeza">Em Limpeza</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Grid de Quartos -->
<div class="row g-3">
    @forelse($quartos as $quarto)
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card h-100 border-start border-4 
            {{ $quarto->estado === 'disponivel' ? 'border-success' : '' }}
            {{ $quarto->estado === 'reservado' ? 'border-warning' : '' }}
            {{ $quarto->estado === 'ocupado' ? 'border-danger' : '' }}
            {{ $quarto->estado === 'limpeza' ? 'border-info' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">Quarto {{ $quarto->numero }}</h5>
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
                <p class="mb-3"><strong>R$ {{ number_format($quarto->preco_diaria, 2, ',', '.') }}/dia</strong></p>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('quartos.edit', $quarto->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    <select wire:change="alterarEstado({{ $quarto->id }}, $event.target.value)" 
                            class="form-select form-select-sm">
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
@endsection
