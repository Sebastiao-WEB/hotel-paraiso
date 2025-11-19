@extends('layouts.dashboard')

@section('title', 'Limpeza de Quartos')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Gestão de Limpeza</h2>

    <!-- Quartos em Limpeza -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quartos em Limpeza</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($quartosLimpeza as $quarto)
            <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-semibold text-gray-800">Quarto {{ $quarto->numero }}</h4>
                        <p class="text-sm text-gray-600">{{ $quarto->tipo }}</p>
                    </div>
                </div>
                <button wire:click="marcarDisponivel({{ $quarto->id }})" 
                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Marcar como Disponível
                </button>
            </div>
            @empty
            <div class="col-span-full text-center py-4 text-gray-500">
                Nenhum quarto em limpeza
            </div>
            @endforelse
        </div>
    </div>

    <!-- Quartos Ocupados (aguardando limpeza) -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quartos Ocupados (Aguardando Limpeza)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($quartosOcupados as $quarto)
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-semibold text-gray-800">Quarto {{ $quarto->numero }}</h4>
                        <p class="text-sm text-gray-600">{{ $quarto->tipo }}</p>
                    </div>
                </div>
                <button wire:click="marcarEmLimpeza({{ $quarto->id }})" 
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Marcar para Limpeza
                </button>
            </div>
            @empty
            <div class="col-span-full text-center py-4 text-gray-500">
                Nenhum quarto ocupado aguardando limpeza
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection


