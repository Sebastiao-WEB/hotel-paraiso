@extends('layouts.dashboard')

@section('title', $clienteId ? 'Editar Cliente' : 'Novo Cliente')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $clienteId ? 'Editar Cliente' : 'Novo Cliente' }}</h5>
            </div>
            <div class="card-body">
                <form wire:submit="save">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome *</label>
                            <input type="text" wire:model="nome" class="form-control @error('nome') is-invalid @enderror">
                            @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo *</label>
                            <select wire:model="tipo" class="form-select @error('tipo') is-invalid @enderror">
                                <option value="pessoa">Pessoa Física</option>
                                <option value="empresa">Empresa</option>
                            </select>
                            @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" wire:model="telefone" class="form-control @error('telefone') is-invalid @enderror">
                            @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIF</label>
                            <input type="text" wire:model="nif" class="form-control @error('nif') is-invalid @enderror">
                            @error('nif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Endereço</label>
                            <textarea wire:model="endereco" rows="3" class="form-control @error('endereco') is-invalid @enderror"></textarea>
                            @error('endereco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
