@extends('layouts.dashboard')

@section('title', isset($user) ? 'Editar Usuário' : 'Novo Usuário')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ isset($user) ? 'Editar Usuário' : 'Novo Usuário' }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
                    @csrf
                    @if(isset($user))
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" 
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" 
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Senha {{ isset($user) ? '(deixe em branco para manter)' : '*' }}</label>
                            <input type="password" name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   {{ isset($user) ? '' : 'required' }}>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirmar Senha {{ isset($user) ? '(deixe em branco para manter)' : '*' }}</label>
                            <input type="password" name="password_confirmation" 
                                   class="form-control" 
                                   {{ isset($user) ? '' : 'required' }}>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Cargo *</label>
                            <select name="cargo" class="form-select @error('cargo') is-invalid @enderror" required>
                                <option value="">Selecione um cargo</option>
                                <option value="admin" {{ old('cargo', $user->cargo ?? '') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="recepcionista" {{ old('cargo', $user->cargo ?? 'recepcionista') == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                                <option value="limpeza" {{ old('cargo', $user->cargo ?? '') == 'limpeza' ? 'selected' : '' }}>Limpeza</option>
                            </select>
                            @error('cargo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">
                                <strong>Administrador:</strong> Acesso total ao sistema<br>
                                <strong>Recepcionista:</strong> Gestão de reservas, check-in/check-out<br>
                                <strong>Limpeza:</strong> Gestão de limpeza de quartos
                            </small>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

