@extends('layouts.dashboard')

@section('title', 'Serviços Extras')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Serviços Extras</h2>
    <a href="{{ route('admin.servicos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Novo Serviço
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.servicos.index') }}">
            <div class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Buscar serviço..." class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
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
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servicos as $servico)
                    <tr>
                        <td class="fw-semibold">{{ $servico->nome }}</td>
                        <td>MZN {{ number_format($servico->preco, 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('admin.servicos.edit', $servico->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.servicos.destroy', $servico->id) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Tem certeza que deseja excluir este serviço?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">Nenhum serviço encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $servicos->links() }}
    </div>
</div>
@endsection


