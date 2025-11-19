@extends('layouts.public')

@section('title', 'Detalhes do Quarto - Hotel Paraíso')

@section('content')
<div id="room-detail-container">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="mt-3 text-muted">Carregando detalhes do quarto...</p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const container = document.getElementById('room-detail-container');
    const roomId = {{ $id }};
    const urlParams = new URLSearchParams(window.location.search);
    const checkin = urlParams.get('checkin') || '';
    const checkout = urlParams.get('checkout') || '';
    
    if (!roomId) {
        container.innerHTML = '<div class="alert alert-danger">Quarto não encontrado.</div>';
        return;
    }
    
    try {
        const quarto = await buscarQuarto(roomId);
        renderRoomDetail(quarto, checkin, checkout);
    } catch (error) {
        console.error('Erro ao carregar quarto:', error);
        container.innerHTML = '<div class="alert alert-danger">Erro ao carregar detalhes do quarto.</div>';
    }
    
    function renderRoomDetail(quarto, checkin, checkout) {
        const estadoBadge = {
            'disponivel': '<span class="badge bg-success fs-6">Disponível</span>',
            'reservado': '<span class="badge bg-warning fs-6">Reservado</span>',
            'ocupado': '<span class="badge bg-danger fs-6">Ocupado</span>',
            'limpeza': '<span class="badge bg-info fs-6">Em Limpeza</span>'
        };
        
        container.innerHTML = `
            <!-- Banner -->
            <section class="hero-section" style="background-image: url('https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1920'); min-height: 60vh;">
                <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
                <div class="container hero-content text-white text-center d-flex align-items-center justify-content-center" style="min-height: 60vh;">
                    <div>
                        <h1 class="display-4 fw-bold mb-3">Quarto ${quarto.numero}</h1>
                        <p class="lead">${quarto.tipo}</p>
                    </div>
                </div>
            </section>
            
            <!-- Detalhes e Reserva -->
            <section class="section-padding">
                <div class="container">
                    <div class="row g-5">
                        <!-- Informações -->
                        <div class="col-lg-7">
                            <div class="mb-4">
                                ${estadoBadge[quarto.estado] || ''}
                                <h2 class="mt-3 mb-3">Quarto ${quarto.numero} - ${quarto.tipo}</h2>
                                <p class="fs-3 text-primary fw-bold mb-4">MZN ${parseFloat(quarto.preco_diaria).toLocaleString('pt-PT', {minimumFractionDigits: 2})} <small class="fs-6 text-muted">/ noite</small></p>
                            </div>
                            
                            <div class="mb-5">
                                <h4 class="mb-3">Descrição</h4>
                                <p class="text-muted lead">
                                    Quarto espaçoso e elegante, perfeitamente decorado para proporcionar máximo conforto e tranquilidade. 
                                    Ideal para casais ou viajantes individuais que buscam uma experiência única de hospitalidade.
                                </p>
                            </div>
                            
                            <div class="mb-5">
                                <h4 class="mb-3">Comodidades</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-wifi text-primary fs-4 me-3"></i>
                                            <span>Wi-Fi de Alta Velocidade</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-tv text-primary fs-4 me-3"></i>
                                            <span>TV por Cabo com 50+ Canais</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-snow text-primary fs-4 me-3"></i>
                                            <span>Ar Condicionado</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-droplet text-primary fs-4 me-3"></i>
                                            <span>Banheiro Privativo</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-safe text-primary fs-4 me-3"></i>
                                            <span>Cofre</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-telephone text-primary fs-4 me-3"></i>
                                            <span>Telefone</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulário de Reserva -->
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 sticky-top" style="top: 100px;">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Fazer Reserva</h5>
                                </div>
                                <div class="card-body">
                                    <div id="alert-container-reserva"></div>
                                    <form id="reserva-form" class="needs-validation" novalidate>
                                        <input type="hidden" name="quarto_id" value="${quarto.id}">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Nome Completo *</label>
                                            <input type="text" name="nome" class="form-control" required>
                                            <div class="invalid-feedback">Por favor, informe seu nome.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Email *</label>
                                            <input type="email" name="email" class="form-control" required>
                                            <div class="invalid-feedback">Por favor, informe um email válido.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Telefone *</label>
                                            <input type="tel" name="telefone" class="form-control" required>
                                            <div class="invalid-feedback">Por favor, informe seu telefone.</div>
                                        </div>
                                        
                                        <div class="row g-3 mb-3">
                                            <div class="col-6">
                                                <label class="form-label">Data de Entrada *</label>
                                                <input type="date" name="data_entrada" id="reserva-checkin" 
                                                       class="form-control" min="${checkin || '{{ date('Y-m-d') }}'}" 
                                                       value="${checkin}" required>
                                                <div class="invalid-feedback">Selecione a data de entrada.</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Data de Saída *</label>
                                                <input type="date" name="data_saida" id="reserva-checkout" 
                                                       class="form-control" min="${checkout || '{{ date('Y-m-d', strtotime('+1 day')) }}'}" 
                                                       value="${checkout}" required>
                                                <div class="invalid-feedback">Selecione a data de saída.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-3 mb-3">
                                            <div class="col-6">
                                                <label class="form-label">Adultos *</label>
                                                <select name="adultos" class="form-select" required>
                                                    ${Array.from({length: 10}, (_, i) => `<option value="${i+1}">${i+1}</option>`).join('')}
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Crianças</label>
                                                <select name="criancas" class="form-select">
                                                    ${Array.from({length: 6}, (_, i) => `<option value="${i}">${i}</option>`).join('')}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Forma de Pagamento</label>
                                            <select name="tipo_pagamento" class="form-select">
                                                <option value="">Selecione...</option>
                                                <option value="dinheiro">Dinheiro</option>
                                                <option value="cartao">Cartão</option>
                                            </select>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <strong>Total estimado:</strong><br>
                                            <span id="total-estimado">MZN 0,00</span>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                                            <i class="bi bi-check-circle me-2"></i>Reservar Agora
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        `;
        
        // Atualizar total estimado
        const checkinInput = document.getElementById('reserva-checkin');
        const checkoutInput = document.getElementById('reserva-checkout');
        
        function calcularTotal() {
            if (checkinInput.value && checkoutInput.value) {
                const checkin = new Date(checkinInput.value);
                const checkout = new Date(checkoutInput.value);
                const dias = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
                const total = dias * parseFloat(quarto.preco_diaria);
                document.getElementById('total-estimado').textContent = 
                    `MZN ${total.toLocaleString('pt-PT', {minimumFractionDigits: 2})} (${dias} noite(s))`;
            }
        }
        
        checkinInput?.addEventListener('change', function() {
            const checkinDate = new Date(this.value);
            checkinDate.setDate(checkinDate.getDate() + 1);
            if (checkoutInput) {
                checkoutInput.min = checkinDate.toISOString().split('T')[0];
            }
            calcularTotal();
        });
        
        checkoutInput?.addEventListener('change', calcularTotal);
        calcularTotal();
        
        // Submeter formulário
        const form = document.getElementById('reserva-form');
        form?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            showLoading(button);
            
            try {
                const formData = new FormData(form);
                const dados = {
                    cliente_id: null, // Será criado automaticamente
                    quarto_id: quarto.id,
                    data_entrada: formData.get('data_entrada'),
                    data_saida: formData.get('data_saida'),
                    tipo_pagamento: formData.get('tipo_pagamento'),
                    nome_cliente: formData.get('nome'),
                    email_cliente: formData.get('email'),
                    telefone_cliente: formData.get('telefone'),
                };
                
                const resultado = await criarReserva(dados);
                
                showAlert('Reserva realizada com sucesso! Entraremos em contato em breve.', 'success', 'alert-container-reserva');
                form.reset();
                form.classList.remove('was-validated');
                
                // Redirecionar após 3 segundos
                setTimeout(() => {
                    window.location.href = '{{ route('public.home') }}';
                }, 3000);
                
            } catch (error) {
                const mensagem = error.response?.data?.message || 'Erro ao realizar reserva. Tente novamente.';
                showAlert(mensagem, 'danger', 'alert-container-reserva');
            } finally {
                hideLoading(button);
            }
        });
    }
});
</script>
@endpush

@endsection

