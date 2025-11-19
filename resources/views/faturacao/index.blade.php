@extends('layouts.dashboard')

@section('title', 'Faturação')

@section('content')
<h2 class="mb-4">Notas de Cobrança</h2>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.faturacao.index') }}">
            <div class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Buscar por empresa..." class="form-control">
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
                        <th>Número</th>
                        <th>Empresa</th>
                        <th>Reserva</th>
                        <th>Data Emissão</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notas as $nota)
                    <tr>
                        <td class="fw-semibold">#{{ str_pad($nota->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $nota->empresa->nome }}</td>
                        <td>Quarto {{ $nota->reserva->quarto->numero }}</td>
                        <td>{{ $nota->data_emissao->format('d/m/Y H:i') }}</td>
                        <td>MZN {{ number_format($nota->valor_total, 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('admin.faturacao.pdf', $nota->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-pdf me-1"></i> Ver PDF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Nenhuma nota de cobrança encontrada</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $notas->links() }}
    </div>
</div>
@endsection


