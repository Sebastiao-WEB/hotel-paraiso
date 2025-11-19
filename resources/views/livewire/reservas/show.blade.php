@extends('layouts.dashboard')

@section('title', 'Detalhes da Reserva')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Detalhes da Reserva</h2>
            <a href="{{ route('reservas.index') }}" class="text-blue-600 hover:text-blue-800">← Voltar</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Cliente</h3>
                <p class="text-gray-800">{{ $reserva->cliente->nome }}</p>
                <p class="text-sm text-gray-600">{{ $reserva->cliente->tipo === 'empresa' ? 'Empresa' : 'Pessoa Física' }}</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Quarto</h3>
                <p class="text-gray-800">Quarto {{ $reserva->quarto->numero }}</p>
                <p class="text-sm text-gray-600">{{ $reserva->quarto->tipo }}</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Período</h3>
                <p class="text-gray-800">Entrada: {{ $reserva->data_entrada->format('d/m/Y') }}</p>
                <p class="text-gray-800">Saída: {{ $reserva->data_saida->format('d/m/Y') }}</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Status</h3>
                <span class="px-3 py-1 rounded-full text-sm
                    {{ $reserva->status === 'confirmada' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $reserva->status === 'pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $reserva->status === 'checkin' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $reserva->status === 'checkout' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $reserva->status === 'cancelada' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($reserva->status) }}
                </span>
            </div>

            @if($reserva->servicos->count() > 0)
            <div class="md:col-span-2">
                <h3 class="font-semibold text-gray-700 mb-2">Serviços Extras</h3>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Serviço</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Quantidade</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reserva->servicos as $rs)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $rs->servico->nome }}</td>
                            <td class="px-4 py-2">{{ $rs->quantidade }}</td>
                            <td class="px-4 py-2">R$ {{ number_format($rs->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold text-gray-800">Valor Total:</span>
                    <span class="text-2xl font-bold text-blue-600">R$ {{ number_format($reserva->valor_total, 2, ',', '.') }}</span>
                </div>
                @if($reserva->tipo_pagamento)
                <p class="text-sm text-gray-600 mt-2">Forma de Pagamento: {{ ucfirst($reserva->tipo_pagamento) }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


