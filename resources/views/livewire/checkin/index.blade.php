@extends('layouts.dashboard')

@section('title', 'Check-in / Check-out')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Check-in / Check-out</h2>

    <!-- Check-ins Pendentes -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Check-ins Pendentes</h3>
        <div class="space-y-3">
            @forelse($reservasCheckin as $reserva)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-semibold text-gray-800">{{ $reserva->cliente->nome }}</p>
                    <p class="text-sm text-gray-600">Quarto {{ $reserva->quarto->numero }} - Entrada: {{ $reserva->data_entrada->format('d/m/Y') }}</p>
                </div>
                <button wire:click="abrirCheckin({{ $reserva->id }})" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Realizar Check-in
                </button>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Nenhum check-in pendente</p>
            @endforelse
        </div>
    </div>

    <!-- Check-outs Pendentes -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Check-outs Pendentes</h3>
        <div class="space-y-3">
            @forelse($reservasCheckout as $reserva)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-semibold text-gray-800">{{ $reserva->cliente->nome }}</p>
                    <p class="text-sm text-gray-600">Quarto {{ $reserva->quarto->numero }} - Saída: {{ $reserva->data_saida->format('d/m/Y') }}</p>
                    <p class="text-sm font-semibold text-blue-600">Total: R$ {{ number_format($reserva->calcularValorTotal(), 2, ',', '.') }}</p>
                </div>
                <button wire:click="abrirCheckout({{ $reserva->id }})" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Realizar Check-out
                </button>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Nenhum check-out pendente</p>
            @endforelse
        </div>
    </div>

    <!-- Modal Check-in -->
    @if($mostrarCheckin && $reservaSelecionada)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Check-in - {{ $reservaSelecionada->cliente->nome }}</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Quarto: <span class="font-semibold">{{ $reservaSelecionada->quarto->numero }}</span></p>
                    <p class="text-sm text-gray-600">Período: {{ $reservaSelecionada->data_entrada->format('d/m/Y') }} até {{ $reservaSelecionada->data_saida->format('d/m/Y') }}</p>
                </div>
                <div class="flex justify-end space-x-4">
                    <button wire:click="$set('mostrarCheckin', false)" class="px-4 py-2 border border-gray-300 rounded-lg">Cancelar</button>
                    <button wire:click="realizarCheckin" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Confirmar Check-in</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Check-out -->
    @if($mostrarCheckout && $reservaSelecionada)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Check-out - {{ $reservaSelecionada->cliente->nome }}</h3>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Quarto: <span class="font-semibold">{{ $reservaSelecionada->quarto->numero }}</span></p>
                        <p class="text-sm text-gray-600">Período: {{ $reservaSelecionada->data_entrada->format('d/m/Y') }} até {{ $reservaSelecionada->data_saida->format('d/m/Y') }}</p>
                    </div>
                </div>

                <!-- Serviços Extras -->
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">Adicionar Serviços Extras</h4>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        @foreach($servicos as $servico)
                        <button wire:click="adicionarServico({{ $servico->id }})" 
                                class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-left">
                            <p class="font-semibold">{{ $servico->nome }}</p>
                            <p class="text-sm text-gray-600">R$ {{ number_format($servico->preco, 2, ',', '.') }}</p>
                        </button>
                        @endforeach
                    </div>

                    @if($reservaSelecionada->servicos->count() > 0)
                    <div class="border-t pt-4">
                        <h5 class="font-semibold mb-2">Serviços Adicionados:</h5>
                        <ul class="space-y-2">
                            @foreach($reservaSelecionada->servicos as $rs)
                            <li class="flex justify-between text-sm">
                                <span>{{ $rs->servico->nome }} (x{{ $rs->quantidade }})</span>
                                <span>R$ {{ number_format($rs->subtotal, 2, ',', '.') }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Resumo Financeiro -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between mb-2">
                        <span>Diárias:</span>
                        <span>R$ {{ number_format($reservaSelecionada->quarto->preco_diaria * $reservaSelecionada->data_entrada->diffInDays($reservaSelecionada->data_saida), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Serviços Extras:</span>
                        <span>R$ {{ number_format($reservaSelecionada->servicos->sum('subtotal'), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                        <span>Total:</span>
                        <span>R$ {{ number_format($reservaSelecionada->calcularValorTotal(), 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Forma de Pagamento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Forma de Pagamento *</label>
                    <select wire:model="tipoPagamento" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione...</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="cartao">Cartão</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-4">
                    <button wire:click="$set('mostrarCheckout', false)" class="px-4 py-2 border border-gray-300 rounded-lg">Cancelar</button>
                    <button wire:click="realizarCheckout" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Confirmar Check-out</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection


