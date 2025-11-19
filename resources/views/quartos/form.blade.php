@extends('layouts.dashboard')

@section('title', isset($quarto) ? 'Editar Quarto' : 'Novo Quarto')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ isset($quarto) ? 'Editar Quarto' : 'Novo Quarto' }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ isset($quarto) ? route('admin.quartos.update', $quarto->id) : route('admin.quartos.store') }}">
                    @csrf
                    @if(isset($quarto))
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Número *</label>
                            <input type="text" name="numero" value="{{ old('numero', $quarto->numero ?? '') }}" 
                                   class="form-control @error('numero') is-invalid @enderror" required>
                            @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo *</label>
                            <input type="text" name="tipo" value="{{ old('tipo', $quarto->tipo ?? '') }}" 
                                   placeholder="Ex: Standard, Suíte, Deluxe"
                                   class="form-control @error('tipo') is-invalid @enderror" required>
                            @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Preço Diária (MZN) *</label>
                            <input type="number" step="0.01" name="preco_diaria" 
                                   value="{{ old('preco_diaria', $quarto->preco_diaria ?? '') }}" 
                                   class="form-control @error('preco_diaria') is-invalid @enderror" required>
                            @error('preco_diaria') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Estado *</label>
                            <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="disponivel" {{ old('estado', $quarto->estado ?? 'disponivel') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                                <option value="reservado" {{ old('estado', $quarto->estado ?? '') == 'reservado' ? 'selected' : '' }}>Reservado</option>
                                <option value="ocupado" {{ old('estado', $quarto->estado ?? '') == 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                                <option value="limpeza" {{ old('estado', $quarto->estado ?? '') == 'limpeza' ? 'selected' : '' }}>Em Limpeza</option>
                            </select>
                            @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.quartos.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

