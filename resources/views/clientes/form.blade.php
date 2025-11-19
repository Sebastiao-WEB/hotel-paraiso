@extends('layouts.dashboard')

@section('title', isset($cliente) ? 'Editar Cliente' : 'Novo Cliente')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ isset($cliente) ? 'Editar Cliente' : 'Novo Cliente' }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ isset($cliente) ? route('admin.clientes.update', $cliente->id) : route('admin.clientes.store') }}">
                    @csrf
                    @if(isset($cliente))
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" value="{{ old('nome', $cliente->nome ?? '') }}" 
                                   class="form-control @error('nome') is-invalid @enderror" required>
                            @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo *</label>
                            <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                <option value="pessoa" {{ old('tipo', $cliente->tipo ?? 'pessoa') == 'pessoa' ? 'selected' : '' }}>Pessoa Física</option>
                                <option value="empresa" {{ old('tipo', $cliente->tipo ?? '') == 'empresa' ? 'selected' : '' }}>Empresa</option>
                            </select>
                            @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $cliente->email ?? '') }}" 
                                   class="form-control @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" value="{{ old('telefone', $cliente->telefone ?? '') }}" 
                                   class="form-control @error('telefone') is-invalid @enderror">
                            @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIF</label>
                            <input type="text" name="nif" value="{{ old('nif', $cliente->nif ?? '') }}" 
                                   class="form-control @error('nif') is-invalid @enderror">
                            @error('nif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Endereço</label>
                            <textarea name="endereco" rows="3" 
                                      class="form-control @error('endereco') is-invalid @enderror">{{ old('endereco', $cliente->endereco ?? '') }}</textarea>
                            @error('endereco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


