@extends('layouts.public')

@section('title', 'Serviços - Hotel Paraíso')

@section('content')
<!-- Hero Section -->
<section class="hero-section d-flex align-items-center" style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920'); min-height: 50vh;">
    <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
    <div class="container hero-content text-white text-center">
        <h1 class="display-4 fw-bold mb-3">Nossos Serviços</h1>
        <p class="lead">Tudo que você precisa para uma estadia perfeita</p>
    </div>
</section>

<!-- Serviços -->
<section class="section-padding">
    <div class="container">
        <div id="loading-spinner" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>
        
        <div id="services-container" class="row g-4">
            <!-- Serviços padrão enquanto carrega -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-cup-hot-fill text-primary fs-1"></i>
                    </div>
                    <h4 class="mb-3">Restaurante</h4>
                    <p class="text-muted">Desfrute de pratos deliciosos preparados pelos nossos chefs experientes. Menu variado com especialidades locais e internacionais.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-water text-primary fs-1"></i>
                    </div>
                    <h4 class="mb-3">Piscina</h4>
                    <p class="text-muted">Relaxe na nossa piscina ao ar livre, perfeita para refrescar-se nos dias quentes de Nampula.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-bag-check-fill text-primary fs-1"></i>
                    </div>
                    <h4 class="mb-3">Lavanderia</h4>
                    <p class="text-muted">Serviço completo de lavanderia e passadoria para manter suas roupas impecáveis durante sua estadia.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-heart-pulse-fill text-primary fs-1"></i>
                    </div>
                    <h4 class="mb-3">Spa & Bem-estar</h4>
                    <p class="text-muted">Relaxe e renove suas energias com nossos tratamentos de spa e massagens terapêuticas.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-car-front-fill text-primary fs-1"></i>
                    </div>
                    <h4 class="mb-3">Transporte</h4>
                    <p class="text-muted">Serviço de transporte disponível para aeroporto e principais pontos da cidade. Conforto e pontualidade garantidos.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-wifi text-primary fs-1"></i>
                    </div>
                    <h4 class="mb-3">Wi-Fi Gratuito</h4>
                    <p class="text-muted">Acesso gratuito à internet de alta velocidade em todo o hotel. Conecte-se sem limites.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const servicos = await buscarServicos();
        if (Array.isArray(servicos) && servicos.length > 0) {
            const container = document.getElementById('services-container');
            container.innerHTML = servicos.map(servico => `
                <div class="col-md-6 col-lg-4">
                    <div class="card card-hover border-0 shadow-sm h-100 text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-star-fill text-primary fs-1"></i>
                        </div>
                        <h4 class="mb-3">${servico.nome}</h4>
                        <p class="text-muted">MZN ${parseFloat(servico.preco).toLocaleString('pt-PT', {minimumFractionDigits: 2})}</p>
                    </div>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Erro ao carregar serviços:', error);
        // Manter serviços padrão se houver erro
    }
});
</script>
@endpush

@endsection


