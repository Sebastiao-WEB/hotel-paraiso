@extends('layouts.dashboard')

@section('title', 'Check-in / Check-out')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Check-in / Check-out</h2>
        <button wire:click="abrirCheckinDireto" 
                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 flex items-center gap-2">
            <i class="bi bi-plus-circle"></i>
            Check-in Direto
        </button>
    </div>

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

    <!-- Check-outs Pendentes (Reservas) -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Check-outs Pendentes (Reservas)</h3>
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

    <!-- Check-outs Pendentes (Estadias Diretas - Walk-in) -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Check-outs Pendentes (Walk-in)
            <span class="text-xs font-normal text-gray-500 ml-2">Estadias diretas sem reserva</span>
        </h3>
        <div class="space-y-3">
            @forelse($staysCheckout as $stay)
            <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg border border-orange-200">
                <div>
                    <p class="font-semibold text-gray-800">
                        {{ $stay->guest->nome }}
                        <span class="text-xs bg-orange-200 text-orange-800 px-2 py-1 rounded ml-2">WALK-IN</span>
                    </p>
                    <p class="text-sm text-gray-600">
                        Quarto {{ $stay->room->numero }} - 
                        Entrada: {{ $stay->check_in_at->format('d/m/Y H:i') }} - 
                        Saída Prevista: {{ $stay->expected_check_out_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Registrado por: {{ $stay->createdBy->name ?? 'N/A' }} em {{ $stay->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <button wire:click="abrirCheckoutStay({{ $stay->id }})" 
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    Realizar Check-out
                </button>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Nenhuma estadia direta aguardando check-out</p>
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

    <!-- Modal Check-in Direto -->
    @if($mostrarCheckinDireto)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Check-in Direto (Sem Reserva)</h3>
            
            @if (session()->has('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if (session()->has('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit.prevent="realizarCheckinDireto" class="space-y-4">
                <!-- Cliente -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                    <select wire:model="cliente_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('cliente_id') border-red-500 @enderror"
                            required>
                        <option value="">Selecione um cliente...</option>
                        @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                        @endforeach
                    </select>
                    @error('cliente_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Quarto -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quarto *</label>
                    <select wire:model="quarto_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('quarto_id') border-red-500 @enderror"
                            required>
                        <option value="">Selecione um quarto...</option>
                        @foreach($quartosDisponiveis as $quarto)
                        <option value="{{ $quarto->id }}">
                            Quarto {{ $quarto->numero }} - {{ $quarto->tipo }} 
                            (R$ {{ number_format($quarto->preco_diaria, 2, ',', '.') }}/dia)
                            @if($quarto->estado === 'limpeza')
                                - Em Limpeza
                            @endif
                        </option>
                        @endforeach
                    </select>
                    @error('quarto_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Data/Hora de Entrada -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data e Hora de Entrada</label>
                    <input type="datetime-local" 
                           wire:model="check_in_at" 
                           max="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('check_in_at') border-red-500 @enderror">
                    <small class="text-gray-500">Deixe em branco para usar o momento atual</small>
                    @error('check_in_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Data/Hora Prevista de Saída -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data e Hora Prevista de Saída *</label>
                    <input type="datetime-local" 
                           wire:model="expected_check_out_at" 
                           min="{{ $check_in_at ? date('Y-m-d\TH:i', strtotime($check_in_at . ' +1 hour')) : now()->addHour()->format('Y-m-d\TH:i') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('expected_check_out_at') border-red-500 @enderror"
                           required>
                    @error('expected_check_out_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Observações -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                    <textarea wire:model="notes" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror"
                              placeholder="Observações adicionais sobre a estadia..."></textarea>
                    @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                @if($quarto_id && $expected_check_out_at)
                @php
                    $quarto = \App\Models\Quarto::find($quarto_id);
                    $entrada = $check_in_at ? \Carbon\Carbon::parse($check_in_at) : now();
                    $saida = \Carbon\Carbon::parse($expected_check_out_at);
                    $horas = $entrada->diffInHours($saida);
                    $dias = max(1, ceil($horas / 24)); // Mínimo 1 dia
                    $valorTotal = $quarto ? $dias * $quarto->preco_diaria : 0;
                @endphp
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between mb-2">
                        <span>Período Estimado:</span>
                        <span>{{ $dias }} dia(s) / {{ $horas }} hora(s)</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Valor da Diária:</span>
                        <span>R$ {{ $quarto ? number_format($quarto->preco_diaria, 2, ',', '.') : '0,00' }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                        <span>Total Estimado:</span>
                        <span>R$ {{ number_format($valorTotal, 2, ',', '.') }}</span>
                    </div>
                    <small class="text-gray-500">* Valor final será calculado no check-out baseado no tempo real</small>
                </div>
                @endif

                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" 
                            wire:click="$set('mostrarCheckinDireto', false)" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Confirmar Check-in
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Check-out Stay (Estadia Direta) -->
    @if($mostrarCheckoutStay && $staySelecionada)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                Check-out - {{ $staySelecionada->guest->nome }}
                <span class="text-xs bg-orange-200 text-orange-800 px-2 py-1 rounded ml-2">WALK-IN</span>
            </h3>
            
            @if (session()->has('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Quarto: <span class="font-semibold">{{ $staySelecionada->room->numero }}</span></p>
                        <p class="text-sm text-gray-600">Entrada: <span class="font-semibold">{{ $staySelecionada->check_in_at->format('d/m/Y H:i') }}</span></p>
                        <p class="text-sm text-gray-600">Saída Prevista: <span class="font-semibold">{{ $staySelecionada->expected_check_out_at->format('d/m/Y H:i') }}</span></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Saída Real: <span class="font-semibold">{{ now()->format('d/m/Y H:i') }}</span></p>
                        <p class="text-sm text-gray-600">Período: <span class="font-semibold">{{ $staySelecionada->nights }} noite(s)</span></p>
                    </div>
                </div>

                <!-- Resumo Financeiro -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between mb-2">
                        <span>Diárias ({{ $staySelecionada->nights }} noite(s)):</span>
                        <span>R$ {{ number_format($staySelecionada->nights * $staySelecionada->room->preco_diaria, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                        <span>Total:</span>
                        <span>R$ {{ number_format($staySelecionada->calculateTotalAmount(), 2, ',', '.') }}</span>
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
                    <button wire:click="$set('mostrarCheckoutStay', false)" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button wire:click="realizarCheckoutStay" 
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                        Confirmar Check-out
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection


