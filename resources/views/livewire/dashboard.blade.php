@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Clientes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_clientes'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Quartos Disponíveis</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['quartos_disponiveis'] }} / {{ $stats['total_quartos'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Reservas Ativas</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['reservas_ativas'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Receita do Mês</p>
                    <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($stats['receita_mes'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Lists -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ocupação Diária -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ocupação (Últimos 7 dias)</h3>
            <div class="h-64 flex items-end justify-between space-x-2">
                @foreach($ocupacao_diaria as $dia)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gray-200 rounded-t" style="height: {{ max(10, ($dia['ocupados'] / max(1, $stats['total_quartos'])) * 100) }}%">
                        <div class="bg-blue-600 w-full rounded-t" style="height: 100%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-2">{{ $dia['data'] }}</p>
                    <p class="text-xs font-semibold text-gray-800">{{ $dia['ocupados'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Próximos Check-ins -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Próximos Check-ins</h3>
            <div class="space-y-3">
                @forelse($proximos_checkins as $reserva)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $reserva->cliente->nome }}</p>
                        <p class="text-sm text-gray-600">Quarto {{ $reserva->quarto->numero }} - {{ $reserva->data_entrada->format('d/m/Y') }}</p>
                    </div>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $reserva->data_entrada->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Nenhum check-in agendado</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Próximos Check-outs -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Próximos Check-outs</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($proximos_checkouts as $reserva)
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="font-semibold text-gray-800">{{ $reserva->cliente->nome }}</p>
                <p class="text-sm text-gray-600">Quarto {{ $reserva->quarto->numero }}</p>
                <p class="text-sm text-gray-600">Saída: {{ $reserva->data_saida->format('d/m/Y') }}</p>
                <p class="text-sm font-semibold text-blue-600 mt-2">R$ {{ number_format($reserva->valor_total, 2, ',', '.') }}</p>
            </div>
            @empty
            <p class="text-gray-500 col-span-full text-center py-4">Nenhum check-out agendado</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

