@extends('layouts.dashboard')

@section('title', 'Limpeza de Quartos')

@section('content')
<h2 class="mb-4">Gestão de Limpeza</h2>

<!-- Quartos em Limpeza -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Quartos em Limpeza</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($quartosLimpeza as $quarto)
            <div class="col-md-6 col-lg-4">
                <div class="card border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">Quarto {{ $quarto->numero }}</h5>
                                <p class="text-muted small mb-0">{{ $quarto->tipo }}</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.limpeza.marcar-disponivel', $quarto->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle me-1"></i> Marcar como Disponível
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-muted text-center py-4">Nenhum quarto em limpeza</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Quartos Ocupados (aguardando limpeza) -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Quartos Ocupados (Aguardando Limpeza)</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($quartosOcupados as $quarto)
            <div class="col-md-6 col-lg-4">
                <div class="card border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">Quarto {{ $quarto->numero }}</h5>
                                <p class="text-muted small mb-0">{{ $quarto->tipo }}</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.limpeza.marcar-limpeza', $quarto->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-broom me-1"></i> Marcar para Limpeza
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-muted text-center py-4">Nenhum quarto ocupado aguardando limpeza</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection


