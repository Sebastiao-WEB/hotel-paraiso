<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Hotel Paraíso</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar text-white p-3" style="width: 250px;">
            <div class="mb-4">
                <h4 class="text-white mb-0">🏨 Hotel Paraíso</h4>
                <small class="text-muted">Sistema de Gestão</small>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-house-door me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" 
                       href="{{ route('admin.clientes.index') }}">
                        <i class="bi bi-people me-2"></i> Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('quartos.*') ? 'active' : '' }}" 
                       href="{{ route('admin.quartos.index') }}">
                        <i class="bi bi-door-open me-2"></i> Quartos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}" 
                       href="{{ route('admin.reservas.index') }}">
                        <i class="bi bi-calendar-check me-2"></i> Reservas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('checkin.*') ? 'active' : '' }}" 
                       href="{{ route('admin.checkin.index') }}">
                        <i class="bi bi-check-circle me-2"></i> Check-in/Check-out
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('faturacao.*') ? 'active' : '' }}" 
                       href="{{ route('admin.faturacao.index') }}">
                        <i class="bi bi-receipt me-2"></i> Faturação
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('limpeza.*') ? 'active' : '' }}" 
                       href="{{ route('admin.limpeza.index') }}">
                        <i class="bi bi-broom me-2"></i> Limpeza
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('servicos.*') ? 'active' : '' }}" 
                       href="{{ route('admin.servicos.index') }}">
                        <i class="bi bi-box-seam me-2"></i> Serviços Extras
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                       href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people-fill me-2"></i> Usuários
                    </a>
                </li>
            </ul>

            <div class="mt-auto pt-3 border-top border-secondary">
                <div class="mb-2">
                    <small class="text-muted">{{ auth()->user()->name }}</small><br>
                    <small class="text-muted">{{ ucfirst(auth()->user()->cargo) }}</small>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm w-100">
                        <i class="bi bi-box-arrow-right me-1"></i> Sair
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-bottom">
                <div class="container-fluid px-4 py-3">
                    <h4 class="mb-0">@yield('title', 'Dashboard')</h4>
                </div>
            </header>

            <!-- Page Content -->
            <main class="container-fluid p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
