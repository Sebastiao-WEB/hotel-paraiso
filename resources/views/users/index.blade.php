@extends('layouts.dashboard')

@section('title', 'Usuários')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Gestão de Usuários</h2>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Novo Usuário
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Buscar por nome ou email..." class="form-control">
                </div>
                <div class="col-md-4">
                    <select name="cargo" class="form-select">
                        <option value="">Todos os cargos</option>
                        <option value="admin" {{ request('cargo') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="recepcionista" {{ request('cargo') == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                        <option value="limpeza" {{ request('cargo') == 'limpeza' ? 'selected' : '' }}>Limpeza</option>
                    </select>
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
                        <th>Email</th>
                        <th>Cargo</th>
                        <th>Reservas Criadas</th>
                        <th>Data de Criação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge 
                                {{ $user->cargo === 'admin' ? 'bg-danger' : '' }}
                                {{ $user->cargo === 'recepcionista' ? 'bg-primary' : '' }}
                                {{ $user->cargo === 'limpeza' ? 'bg-info' : '' }}">
                                {{ ucfirst($user->cargo) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $user->reservasCriadas_count }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Nenhum usuário encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $users->links() }}
    </div>
</div>
@endsection

