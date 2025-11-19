@extends('layouts.dashboard')

@section('title', isset($reserva) ? 'Editar Reserva' : 'Nova Reserva')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ isset($reserva) ? 'Editar Reserva' : 'Nova Reserva' }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ isset($reserva) ? route('admin.reservas.update', $reserva->id) : route('admin.reservas.store') }}">
                    @csrf
                    @if(isset($reserva))
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Cliente *</label>
                            <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" 
                                    {{ old('cliente_id', $reserva->cliente_id ?? '') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome }} ({{ $cliente->tipo === 'empresa' ? 'Empresa' : 'Pessoa' }})
                                </option>
                                @endforeach
                            </select>
                            @error('cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Data de Entrada *</label>
                            <input type="date" name="data_entrada" id="data_entrada"
                                   value="{{ old('data_entrada', isset($reserva) ? $reserva->data_entrada->format('Y-m-d') : date('Y-m-d')) }}" 
                                   class="form-control @error('data_entrada') is-invalid @enderror" required>
                            @error('data_entrada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Data de Saída *</label>
                            <input type="date" name="data_saida" id="data_saida"
                                   value="{{ old('data_saida', isset($reserva) ? $reserva->data_saida->format('Y-m-d') : date('Y-m-d', strtotime('+1 day'))) }}" 
                                   class="form-control @error('data_saida') is-invalid @enderror" required>
                            @error('data_saida') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Quarto Disponível *</label>
                            <select name="quarto_id" id="quarto_id" 
                                    class="form-select @error('quarto_id') is-invalid @enderror" required>
                                <option value="">Selecione um quarto</option>
                                @if(isset($reserva))
                                    @foreach($quartosDisponiveis as $quarto)
                                    <option value="{{ $quarto->id }}" 
                                        {{ old('quarto_id', $reserva->quarto_id) == $quarto->id ? 'selected' : '' }}>
                                        Quarto {{ $quarto->numero }} - {{ $quarto->tipo }} (MZN {{ number_format($quarto->preco_diaria, 2, ',', '.') }}/dia)
                                    </option>
                                    @endforeach
                                @else
                                    @foreach($quartosDisponiveis as $quarto)
                                    <option value="{{ $quarto->id }}">
                                        Quarto {{ $quarto->numero }} - {{ $quarto->tipo }} (MZN {{ number_format($quarto->preco_diaria, 2, ',', '.') }}/dia)
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('quarto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Os quartos serão atualizados automaticamente ao alterar as datas</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Forma de Pagamento</label>
                            <select name="tipo_pagamento" class="form-select @error('tipo_pagamento') is-invalid @enderror">
                                <option value="">Selecione...</option>
                                <option value="dinheiro" {{ old('tipo_pagamento', $reserva->tipo_pagamento ?? '') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                <option value="cartao" {{ old('tipo_pagamento', $reserva->tipo_pagamento ?? '') == 'cartao' ? 'selected' : '' }}>Cartão</option>
                            </select>
                            @error('tipo_pagamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.reservas.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

