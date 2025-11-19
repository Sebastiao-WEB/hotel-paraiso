@extends('layouts.dashboard')

@section('title', isset($servico) ? 'Editar Serviço' : 'Novo Serviço')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ isset($servico) ? 'Editar Serviço' : 'Novo Serviço' }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ isset($servico) ? route('admin.servicos.update', $servico->id) : route('admin.servicos.store') }}">
                    @csrf
                    @if(isset($servico))
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" value="{{ old('nome', $servico->nome ?? '') }}" 
                                   placeholder="Ex: Restaurante, Lavanderia, Minibar"
                                   class="form-control @error('nome') is-invalid @enderror" required>
                            @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Preço (MZN) *</label>
                            <input type="number" step="0.01" name="preco" 
                                   value="{{ old('preco', $servico->preco ?? '') }}" 
                                   class="form-control @error('preco') is-invalid @enderror" required>
                            @error('preco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.servicos.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


