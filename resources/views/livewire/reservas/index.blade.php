@extends('layouts.dashboard')

@section('title', 'Reservas')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Gestão de Reservas</h2>
        <a href="{{ route('reservas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nova Reserva
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" wire:model.live="search" placeholder="Buscar por cliente..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <select wire:model.live="statusFiltro" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos os status</option>
                    <option value="pendente">Pendente</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="checkin">Check-in</option>
                    <option value="checkout">Check-out</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
            <div>
                <input type="date" wire:model.live="dataFiltro" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quarto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entrada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saída</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reservas as $reserva)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $reserva->cliente->nome }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reserva->quarto->numero }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reserva->data_entrada->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reserva->data_saida->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $reserva->status === 'confirmada' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $reserva->status === 'pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $reserva->status === 'checkin' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $reserva->status === 'checkout' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $reserva->status === 'cancelada' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($reserva->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">R$ {{ number_format($reserva->valor_total, 2, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            @if($reserva->status === 'pendente')
                                <button wire:click="confirmar({{ $reserva->id }})" class="text-green-600 hover:text-green-900">Confirmar</button>
                            @endif
                            @if(!in_array($reserva->status, ['checkout', 'cancelada']))
                                <button wire:click="cancelar({{ $reserva->id }})" class="text-red-600 hover:text-red-900">Cancelar</button>
                            @endif
                            <a href="{{ route('reservas.show', $reserva->id) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nenhuma reserva encontrada</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div>
        {{ $reservas->links() }}
    </div>
</div>
@endsection


