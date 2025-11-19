@extends('layouts.public')

@section('title', 'Sobre Nós - Hotel Paraíso')

@section('content')
<!-- Hero Section -->
<section class="hero-section d-flex align-items-center" style="background-image: url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1920'); min-height: 50vh;">
    <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
    <div class="container hero-content text-white text-center">
        <h1 class="display-4 fw-bold mb-3">Sobre o Hotel Paraíso</h1>
        <p class="lead">Sua casa longe de casa em Nampula</p>
    </div>
</section>

<!-- História -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800" 
                     alt="Hotel Paraíso" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-award-fill text-warning fs-3 me-3"></i>
                    <span class="text-uppercase text-muted fw-bold small">Nossa História</span>
                </div>
                <h2 class="display-5 fw-bold mb-4">Hotel de Luxo no Coração de Nampula</h2>
                <p class="lead text-muted mb-4">
                    O Hotel Paraíso está localizado no coração de Nampula, oferecendo uma experiência única de hospitalidade e conforto. Com detalhes elegantes e acomodações requintadas, somos o reflexo perfeito da tradição moçambicana no mundo moderno.
                </p>
                <p class="text-muted mb-4">
                    Nossa localização privilegiada fica a poucos passos dos principais pontos turísticos, restaurantes e áreas comerciais da cidade. Cada quarto foi cuidadosamente projetado para proporcionar máximo conforto e tranquilidade aos nossos hóspedes.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Missão, Visão e Valores -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4">
                    <i class="bi bi-bullseye text-primary fs-1 mb-3"></i>
                    <h4 class="mb-3">Missão</h4>
                    <p class="text-muted">Proporcionar uma experiência memorável de hospitalidade, combinando conforto, elegância e o calor humano característico de Moçambique.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <i class="bi bi-eye text-primary fs-1 mb-3"></i>
                    <h4 class="mb-3">Visão</h4>
                    <p class="text-muted">Ser reconhecido como o melhor hotel de Nampula, referência em hospitalidade e excelência no atendimento.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <i class="bi bi-heart-fill text-primary fs-1 mb-3"></i>
                    <h4 class="mb-3">Valores</h4>
                    <p class="text-muted">Hospitalidade, excelência, respeito, sustentabilidade e compromisso com a satisfação dos nossos hóspedes.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Localização -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="display-5 fw-bold mb-4">Nossa Localização</h2>
                <p class="lead text-muted mb-4">
                    Situado estrategicamente no centro de Nampula, o Hotel Paraíso oferece fácil acesso aos principais pontos de interesse da cidade.
                </p>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                        <strong>Endereço:</strong> Av. Principal, Nampula, Moçambique
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-telephone-fill text-primary me-2"></i>
                        <strong>Telefone:</strong> +258 84 123 4567
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                        <strong>Email:</strong> info@hotelparaiso.co.mz
                    </li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3834.123456789!2d39.2667!3d-15.1167!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTXCsDA3JzAwLjEiUyAzOcKwMTYnMDAuMSJF!5e0!3m2!1spt-PT!2smz!4v1234567890123!5m2!1spt-PT!2smz" 
                            style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection


