@extends('layouts.dashboard')

@section('title', 'Faturação')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Notas de Cobrança</h2>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4">
        <input type="text" wire:model.live="search" placeholder="Buscar por empresa..." 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserva</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Emissão</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($notas as $nota)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ str_pad($nota->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $nota->empresa->nome }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Quarto {{ $nota->reserva->quarto->numero }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $nota->data_emissao->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">R$ {{ number_format($nota->valor_total, 2, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('faturacao.pdf', $nota->id) }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                            Ver PDF
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhuma nota de cobrança encontrada</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div>
        {{ $notas->links() }}
    </div>
</div>
@endsection


