@extends('layouts.public')

@section('title', 'Hotel Paraíso - O Refúgio Perfeito')

@section('content')
<!-- Hero Section -->
<section class="hero-section d-flex align-items-center" style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920');">
    <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white animate__animated animate__fadeInLeft">
                <h1 class="display-3 fw-bold mb-4" style="font-family: 'Brush Script MT', cursive;">HOLIDAY SEASON</h1>
                <p class="lead fs-4 mb-5">O refúgio perfeito no coração de Nampula</p>
            </div>
            <div class="col-lg-6 animate__animated animate__fadeInRight">
                <div class="booking-widget text-white">
                    <h3 class="mb-4 text-center">Verificar Disponibilidade</h3>
                    <form id="availability-form" class="needs-validation" novalidate>
                        <div id="alert-container"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Data de Entrada</label>
                                <input type="date" id="checkin" name="checkin" class="form-control" 
                                       min="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback">Por favor, selecione uma data de entrada.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data de Saída</label>
                                <input type="date" id="checkout" name="checkout" class="form-control" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                <div class="invalid-feedback">Por favor, selecione uma data de saída.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Adultos</label>
                                <select id="adultos" name="adultos" class="form-select" required>
                                    @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Crianças</label>
                                <select id="criancas" name="criancas" class="form-select">
                                    @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-light w-100 fw-bold">
                                    <i class="bi bi-search me-2"></i>Verificar Disponibilidade
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="availability-results" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Destaques -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-hover border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-star-fill text-warning fs-1"></i>
                    </div>
                    <h4 class="mb-3">Melhores Tarifas</h4>
                    <p class="text-muted">Garantimos as melhores tarifas quando você reserva diretamente conosco. Sem taxas adicionais.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-hover border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-door-open text-primary fs-1"></i>
                    </div>
                    <h4 class="mb-3">Coleção de Quartos</h4>
                    <p class="text-muted">Quartos elegantes e confortáveis, cada um projetado para proporcionar uma experiência única.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-hover border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-geo-alt-fill text-danger fs-1"></i>
                    </div>
                    <h4 class="mb-3">Localização Perfeita</h4>
                    <p class="text-muted">Situado no coração de Nampula, próximo aos principais pontos turísticos e comerciais.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sobre o Hotel -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800" 
                     alt="Hotel Paraíso" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-award-fill text-warning fs-3 me-3"></i>
                    <span class="text-uppercase text-muted fw-bold small">Sobre Nós</span>
                </div>
                <h2 class="display-5 fw-bold mb-4">Hotel de Luxo no Coração de Nampula</h2>
                <p class="lead text-muted mb-4">
                    O Hotel Paraíso está localizado no coração de Nampula, oferecendo uma experiência única de hospitalidade e conforto. Com detalhes elegantes e acomodações requintadas, somos o reflexo perfeito da tradição moçambicana no mundo moderno.
                </p>
                <p class="text-muted mb-4">
                    Nossa localização privilegiada fica a poucos passos dos principais pontos turísticos, restaurantes e áreas comerciais da cidade. Cada quarto foi cuidadosamente projetado para proporcionar máximo conforto e tranquilidade aos nossos hóspedes.
                </p>
                <a href="{{ route('public.about') }}" class="btn btn-primary">
                    Saiba Mais <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Benefícios -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="display-5 fw-bold mb-4">Benefícios ao Reservar Direto</h2>
                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-star-fill text-warning fs-4 me-3 mt-1"></i>
                    <div>
                        <h5>Melhor Garantia de Preço</h5>
                        <p class="text-muted mb-0">Garantimos o melhor preço quando você reserva diretamente em nosso site.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-clock-fill text-primary fs-4 me-3 mt-1"></i>
                    <div>
                        <h5>Reservas 24/7</h5>
                        <p class="text-muted mb-0">Nossa equipe está disponível 24 horas por dia para atender suas necessidades.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-wifi text-info fs-4 me-3 mt-1"></i>
                    <div>
                        <h5>Wi-Fi de Alta Velocidade</h5>
                        <p class="text-muted mb-0">Acesso gratuito à internet de alta velocidade em todo o hotel.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <i class="bi bi-x-circle-fill text-danger fs-4 me-3 mt-1"></i>
                    <div>
                        <h5>Sem Taxa de Reserva</h5>
                        <p class="text-muted mb-0">Reserve diretamente conosco e não pague taxas adicionais.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800" 
                     alt="Amenidades" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('availability-form');
    const checkinInput = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    
    // Atualizar data mínima de checkout quando checkin mudar
    checkinInput.addEventListener('change', function() {
        const checkinDate = new Date(this.value);
        checkinDate.setDate(checkinDate.getDate() + 1);
        checkoutInput.min = checkinDate.toISOString().split('T')[0];
        if (checkoutInput.value && checkoutInput.value <= this.value) {
            checkoutInput.value = checkoutInput.min;
        }
    });
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        const button = form.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        showLoading(button);
        
        try {
            const checkin = checkinInput.value;
            const checkout = checkoutInput.value;
            const adultos = document.getElementById('adultos').value;
            const criancas = document.getElementById('criancas').value;
            
            const quartos = await verificarDisponibilidade(checkin, checkout, adultos, criancas);
            
            const resultsDiv = document.getElementById('availability-results');
            if (quartos.length > 0) {
                resultsDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h5><i class="bi bi-check-circle me-2"></i>Encontramos ${quartos.length} quarto(s) disponível(eis)!</h5>
                        <p class="mb-2">Período: ${new Date(checkin).toLocaleDateString('pt-PT')} até ${new Date(checkout).toLocaleDateString('pt-PT')}</p>
                        <a href="{{ route('public.rooms') }}?checkin=${checkin}&checkout=${checkout}" class="btn btn-light btn-sm">
                            Ver Quartos Disponíveis <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                `;
            } else {
                resultsDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Não encontramos quartos disponíveis para o período selecionado. Tente outras datas.
                    </div>
                `;
            }
        } catch (error) {
            showAlert('Erro ao verificar disponibilidade. Tente novamente.', 'danger', 'alert-container');
        } finally {
            hideLoading(button, originalText);
        }
    });
});
</script>
@endpush

@endsection


