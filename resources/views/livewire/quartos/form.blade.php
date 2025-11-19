@extends('layouts.dashboard')

@section('title', $quartoId ? 'Editar Quarto' : 'Novo Quarto')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $quartoId ? 'Editar Quarto' : 'Novo Quarto' }}</h5>
            </div>
            <div class="card-body">
                <form wire:submit="save">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Número *</label>
                            <input type="text" wire:model="numero" class="form-control @error('numero') is-invalid @enderror">
                            @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo *</label>
                            <input type="text" wire:model="tipo" placeholder="Ex: Standard, Suíte, Deluxe"
                                   class="form-control @error('tipo') is-invalid @enderror">
                            @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Preço Diária (R$) *</label>
                            <input type="number" step="0.01" wire:model="preco_diaria" 
                                   class="form-control @error('preco_diaria') is-invalid @enderror">
                            @error('preco_diaria') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Estado *</label>
                            <select wire:model="estado" class="form-select @error('estado') is-invalid @enderror">
                                <option value="disponivel">Disponível</option>
                                <option value="reservado">Reservado</option>
                                <option value="ocupado">Ocupado</option>
                                <option value="limpeza">Em Limpeza</option>
                            </select>
                            @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('quartos.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
