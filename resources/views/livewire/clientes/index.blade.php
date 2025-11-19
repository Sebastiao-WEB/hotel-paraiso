@extends('layouts.dashboard')

@section('title', 'Clientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Gestão de Clientes</h2>
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Novo Cliente
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" wire:model.live="search" placeholder="Buscar por nome, email ou telefone..." 
                       class="form-control">
            </div>
            <div class="col-md-6">
                <select wire:model.live="tipoFiltro" class="form-select">
                    <option value="">Todos os tipos</option>
                    <option value="pessoa">Pessoa Física</option>
                    <option value="empresa">Empresa</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Tabela -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>NIF</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                    <tr>
                        <td class="fw-semibold">{{ $cliente->nome }}</td>
                        <td>
                            <span class="badge {{ $cliente->tipo === 'empresa' ? 'bg-purple' : 'bg-primary' }}">
                                {{ $cliente->tipo === 'empresa' ? 'Empresa' : 'Pessoa' }}
                            </span>
                        </td>
                        <td>{{ $cliente->email ?? '-' }}</td>
                        <td>{{ $cliente->telefone ?? '-' }}</td>
                        <td>{{ $cliente->nif ?? '-' }}</td>
                        <td>
                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button wire:click="delete({{ $cliente->id }})" 
                                    wire:confirm="Tem certeza que deseja excluir este cliente?"
                                    class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Nenhum cliente encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $clientes->links() }}
    </div>
</div>
@endsection
