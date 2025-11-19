@extends('layouts.dashboard')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalhes do Usuário</h5>
                <div>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary me-2">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Nome</h6>
                        <p class="fw-bold fs-5">{{ $user->name }}</p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Email</h6>
                        <p class="fw-bold">{{ $user->email }}</p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Cargo</h6>
                        <span class="badge 
                            {{ $user->cargo === 'admin' ? 'bg-danger' : '' }}
                            {{ $user->cargo === 'recepcionista' ? 'bg-primary' : '' }}
                            {{ $user->cargo === 'limpeza' ? 'bg-info' : '' }} fs-6">
                            {{ ucfirst($user->cargo) }}
                        </span>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Data de Criação</h6>
                        <p class="mb-0">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($user->reservasCriadas()->count() > 0)
                    <div class="col-12">
                        <h6 class="text-muted mb-3">Reservas Criadas</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Quarto</th>
                                        <th>Entrada</th>
                                        <th>Saída</th>
                                        <th>Status</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->reservasCriadas()->with(['cliente', 'quarto'])->latest()->take(10)->get() as $reserva)
                                    <tr>
                                        <td>{{ $reserva->cliente->nome }}</td>
                                        <td>Quarto {{ $reserva->quarto->numero }}</td>
                                        <td>{{ $reserva->data_entrada->format('d/m/Y') }}</td>
                                        <td>{{ $reserva->data_saida->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge 
                                                {{ $reserva->status === 'confirmada' ? 'bg-success' : '' }}
                                                {{ $reserva->status === 'pendente' ? 'bg-warning' : '' }}
                                                {{ $reserva->status === 'checkin' ? 'bg-primary' : '' }}
                                                {{ $reserva->status === 'checkout' ? 'bg-secondary' : '' }}
                                                {{ $reserva->status === 'cancelada' ? 'bg-danger' : '' }}">
                                                {{ ucfirst($reserva->status) }}
                                            </span>
                                        </td>
                                        <td>MZN {{ number_format($reserva->valor_total, 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small mt-2">
                            Total: {{ $user->reservasCriadas()->count() }} reserva(s)
                        </p>
                    </div>
                    @endif

                    @if($user->reservasCheckin()->count() > 0)
                    <div class="col-12">
                        <h6 class="text-muted mb-2">Check-ins Realizados</h6>
                        <p class="mb-0">{{ $user->reservasCheckin()->count() }} check-in(s)</p>
                    </div>
                    @endif

                    @if($user->reservasCheckout()->count() > 0)
                    <div class="col-12">
                        <h6 class="text-muted mb-2">Check-outs Realizados</h6>
                        <p class="mb-0">{{ $user->reservasCheckout()->count() }} check-out(s)</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

