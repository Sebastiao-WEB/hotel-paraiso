<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hotel Paraíso - O Refúgio Perfeito')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-color: #ff6b35;
            --secondary-color: #8b4513;
            --dark-color: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background-color: rgba(44, 62, 80, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }
        
        .navbar.scrolled {
            background-color: rgba(44, 62, 80, 0.98) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #e55a2b;
            border-color: #e55a2b;
        }
        
        .hero-section {
            min-height: 90vh;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }
        
        .hero-overlay {
            background: linear-gradient(135deg, rgba(44, 62, 80, 0.7) 0%, rgba(0, 0, 0, 0.5) 100%);
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .booking-widget {
            background: rgba(255, 107, 53, 0.95);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .section-padding {
            padding: 80px 0;
        }
        
        .card-hover {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        
        footer {
            background-color: var(--dark-color);
            color: #fff;
        }
        
        .footer-link {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-link:hover {
            color: var(--primary-color);
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('public.home') }}">
                🏨 Hotel Paraíso
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.home') ? 'active' : '' }}" href="{{ route('public.home') }}">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.rooms*') ? 'active' : '' }}" href="{{ route('public.rooms') }}">Quartos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.services') ? 'active' : '' }}" href="{{ route('public.services') }}">Serviços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.about') ? 'active' : '' }}" href="{{ route('public.about') }}">Sobre</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.contact') ? 'active' : '' }}" href="{{ route('public.contact') }}">Contato</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-primary btn-sm" href="{{ route('login') }}">Área Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="mb-3 text-white">🏨 Hotel Paraíso</h5>
                    <p class="text-white">O refúgio perfeito no coração de Nampula. Oferecemos conforto, elegância e hospitalidade excepcional.</p>
                    <div class="mt-3">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-youtube fs-5"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3 text-white">Links Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('public.home') }}" class="text-white footer-link">Início</a></li>
                        <li><a href="{{ route('public.rooms') }}" class="text-white footer-link">Quartos</a></li>
                        <li><a href="{{ route('public.services') }}" class="text-white footer-link">Serviços</a></li>
                        <li><a href="{{ route('public.about') }}" class="text-white footer-link">Sobre Nós</a></li>
                        <li><a href="{{ route('public.contact') }}" class="text-white footer-link">Contato</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3 text-white">Contato</h5>
                    <p class="text-white mb-2">
                        <i class="bi bi-geo-alt me-2"></i> Av. Principal, Nampula, Moçambique
                    </p>
                    <p class="text-white mb-2">
                        <i class="bi bi-telephone me-2"></i> +258 84 123 4567
                    </p>
                    <p class="text-white mb-2">
                        <i class="bi bi-envelope me-2"></i> info@hotelparaiso.co.mz
                    </p>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="text-center">
                <p class="mb-0 text-white">&copy; {{ date('Y') }} Hotel Paraíso. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        // Configuração do Axios
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['Accept'] = 'application/json';

        // Token CSRF
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }

        // Base URL da API
        const API_BASE_URL = '/api';

        // Função para mostrar loading
        function showLoading(element) {
            if (element) {
                const originalText = element.innerHTML;
                element.setAttribute('data-original-text', originalText);
                element.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Carregando...';
                element.disabled = true;
            }
        }

        // Função para esconder loading
        function hideLoading(element) {
            if (element) {
                const originalText = element.getAttribute('data-original-text') || 'Enviar';
                element.innerHTML = originalText;
                element.disabled = false;
            }
        }

        // Função para mostrar alerta
        function showAlert(message, type = 'success', containerId = 'alert-container') {
            const container = document.getElementById(containerId);
            if (!container) {
                // Criar container se não existir
                const newContainer = document.createElement('div');
                newContainer.id = containerId;
                newContainer.className = 'container mt-3';
                document.querySelector('main').prepend(newContainer);
                container = newContainer;
            }
            
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            container.appendChild(alert);
            
            // Auto-remover após 5 segundos
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        // Verificar disponibilidade de quartos
        async function verificarDisponibilidade(checkin, checkout, adultos = 1, criancas = 0) {
            try {
                const response = await axios.get(`${API_BASE_URL}/quartos/disponiveis`, {
                    params: {
                        data_entrada: checkin,
                        data_saida: checkout,
                        adultos: adultos,
                        criancas: criancas
                    }
                });
                return Array.isArray(response.data) ? response.data : (response.data?.data || []);
            } catch (error) {
                console.error('Erro ao verificar disponibilidade:', error);
                throw error;
            }
        }

        // Buscar todos os quartos
        async function buscarQuartos(filtros = {}) {
            try {
                const response = await axios.get(`${API_BASE_URL}/quartos`, {
                    params: filtros
                });
                return Array.isArray(response.data) ? response.data : (response.data?.data || response.data || []);
            } catch (error) {
                console.error('Erro ao buscar quartos:', error);
                throw error;
            }
        }

        // Buscar detalhes de um quarto
        async function buscarQuarto(id) {
            try {
                const response = await axios.get(`${API_BASE_URL}/quartos/${id}`);
                return response.data;
            } catch (error) {
                console.error('Erro ao buscar quarto:', error);
                throw error;
            }
        }

        // Criar reserva
        async function criarReserva(dados) {
            try {
                const response = await axios.post(`${API_BASE_URL}/reservas`, dados);
                return response.data;
            } catch (error) {
                console.error('Erro ao criar reserva:', error);
                throw error;
            }
        }

        // Buscar serviços
        async function buscarServicos() {
            try {
                const response = await axios.get(`${API_BASE_URL}/servicos`);
                return Array.isArray(response.data) ? response.data : (response.data?.data || []);
            } catch (error) {
                console.error('Erro ao buscar serviços:', error);
                throw error;
            }
        }

        // Enviar mensagem de contato
        async function enviarContato(dados) {
            try {
                const response = await axios.post(`${API_BASE_URL}/contatos`, dados);
                return response.data;
            } catch (error) {
                console.error('Erro ao enviar contato:', error);
                throw error;
            }
        }

        // Validação de formulário Bootstrap
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>

