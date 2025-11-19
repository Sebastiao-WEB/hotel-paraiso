@extends('layouts.dashboard')

@section('title', $reservaId ? 'Editar Reserva' : 'Nova Reserva')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ $reservaId ? 'Editar Reserva' : 'Nova Reserva' }}</h2>

        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                    <select wire:model="cliente_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Selecione um cliente</option>
                        @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nome }} ({{ $cliente->tipo === 'empresa' ? 'Empresa' : 'Pessoa' }})</option>
                        @endforeach
                    </select>
                    @error('cliente_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data de Entrada *</label>
                    <input type="date" wire:model.live="data_entrada" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('data_entrada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data de Saída *</label>
                    <input type="date" wire:model.live="data_saida" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('data_saida') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quarto Disponível *</label>
                    <select wire:model="quarto_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Selecione um quarto</option>
                        @forelse($quartosDisponiveis as $quarto)
                        <option value="{{ $quarto->id }}">
                            Quarto {{ $quarto->numero }} - {{ $quarto->tipo }} (R$ {{ number_format($quarto->preco_diaria, 2, ',', '.') }}/dia)
                        </option>
                        @empty
                        <option value="" disabled>Nenhum quarto disponível para estas datas</option>
                        @endforelse
                    </select>
                    @error('quarto_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    @if(count($quartosDisponiveis) === 0 && $data_entrada && $data_saida)
                    <p class="text-yellow-600 text-sm mt-2">⚠️ Nenhum quarto disponível para o período selecionado</p>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Forma de Pagamento</label>
                    <select wire:model="tipo_pagamento" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Selecione...</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="cartao">Cartão</option>
                    </select>
                    @error('tipo_pagamento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('reservas.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


