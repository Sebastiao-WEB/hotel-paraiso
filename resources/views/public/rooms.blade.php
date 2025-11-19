@extends('layouts.public')

@section('title', 'Quartos - Hotel Paraíso')

@section('content')
<!-- Hero Section -->
<section class="hero-section d-flex align-items-center" style="background-image: url('https://images.unsplash.com/photo-1590490360182-c33d57733427?w=1920'); min-height: 50vh;">
    <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
    <div class="container hero-content text-white text-center">
        <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInDown">Nossos Quartos</h1>
        <p class="lead animate__animated animate__fadeInUp">Descubra o conforto e elegância que você merece</p>
    </div>
</section>

<!-- Filtros -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <form id="filter-form" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tipo de Quarto</label>
                <select id="tipo-filter" class="form-select">
                    <option value="">Todos os tipos</option>
                    <option value="Standard">Standard</option>
                    <option value="Superior">Superior</option>
                    <option value="Deluxe">Deluxe</option>
                    <option value="Suíte">Suíte</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Entrada</label>
                <input type="date" id="checkin-filter" class="form-control" min="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Saída</label>
                <input type="date" id="checkout-filter" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-2"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Lista de Quartos -->
<section class="section-padding">
    <div class="container">
        <div id="loading-spinner" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-3 text-muted">Carregando quartos...</p>
        </div>
        
        <div id="alert-container"></div>
        
        <div id="rooms-container" class="row g-4">
            <!-- Quartos serão carregados aqui via JavaScript -->
        </div>
        
        <div id="no-rooms" class="text-center py-5" style="display: none;">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="text-muted mt-3">Nenhum quarto encontrado com os filtros selecionados.</p>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const roomsContainer = document.getElementById('rooms-container');
    const loadingSpinner = document.getElementById('loading-spinner');
    const noRooms = document.getElementById('no-rooms');
    const filterForm = document.getElementById('filter-form');
    
    // Carregar quartos ao carregar a página
    await carregarQuartos();
    
    // Filtrar quando o formulário for submetido
    filterForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        await carregarQuartos();
    });
    
    // Atualizar checkout quando checkin mudar
    document.getElementById('checkin-filter')?.addEventListener('change', function() {
        const checkinDate = new Date(this.value);
        checkinDate.setDate(checkinDate.getDate() + 1);
        const checkoutInput = document.getElementById('checkout-filter');
        if (checkoutInput) {
            checkoutInput.min = checkinDate.toISOString().split('T')[0];
        }
    });
    
    async function carregarQuartos() {
        loadingSpinner.style.display = 'block';
        roomsContainer.innerHTML = '';
        noRooms.style.display = 'none';
        
        try {
            const tipo = document.getElementById('tipo-filter')?.value || '';
            const checkin = document.getElementById('checkin-filter')?.value || '';
            const checkout = document.getElementById('checkout-filter')?.value || '';
            
            let quartos;
            
            if (checkin && checkout) {
                // Buscar apenas quartos disponíveis
                quartos = await verificarDisponibilidade(checkin, checkout);
            } else {
                // Buscar todos os quartos
                const response = await buscarQuartos({ tipo: tipo });
                quartos = response.data || response;
            }
            
            if (Array.isArray(quartos) && quartos.length > 0) {
                quartos.forEach(quarto => {
                    const card = criarCardQuarto(quarto, checkin, checkout);
                    roomsContainer.appendChild(card);
                });
            } else {
                noRooms.style.display = 'block';
            }
        } catch (error) {
            console.error('Erro ao carregar quartos:', error);
            showAlert('Erro ao carregar quartos. Tente novamente.', 'danger', 'alert-container');
        } finally {
            loadingSpinner.style.display = 'none';
        }
    }
    
    function criarCardQuarto(quarto, checkin = '', checkout = '') {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        
        const estadoBadge = {
            'disponivel': '<span class="badge bg-success">Disponível</span>',
            'reservado': '<span class="badge bg-warning">Reservado</span>',
            'ocupado': '<span class="badge bg-danger">Ocupado</span>',
            'limpeza': '<span class="badge bg-info">Em Limpeza</span>'
        };
        
        const url = `{{ route('public.room-detail', ':id') }}`.replace(':id', quarto.id);
        if (checkin && checkout) {
            url += `?checkin=${checkin}&checkout=${checkout}`;
        }
        
        col.innerHTML = `
            <div class="card card-hover border-0 shadow-sm h-100">
                <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600" 
                     class="card-img-top" alt="${quarto.tipo}" style="height: 250px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title">Quarto ${quarto.numero}</h5>
                        ${estadoBadge[quarto.estado] || ''}
                    </div>
                    <p class="text-muted mb-2">${quarto.tipo}</p>
                    <p class="fs-4 text-primary fw-bold mb-3">MZN ${parseFloat(quarto.preco_diaria).toLocaleString('pt-PT', {minimumFractionDigits: 2})}/noite</p>
                    <ul class="list-unstyled mb-3">
                        <li><i class="bi bi-wifi text-primary me-2"></i>Wi-Fi Gratuito</li>
                        <li><i class="bi bi-tv text-primary me-2"></i>TV por Cabo</li>
                        <li><i class="bi bi-snow text-primary me-2"></i>Ar Condicionado</li>
                        <li><i class="bi bi-droplet text-primary me-2"></i>Banheiro Privativo</li>
                    </ul>
                    <a href="${url}" class="btn btn-primary mt-auto">
                        Ver Detalhes <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        `;
        
        return col;
    }
});
</script>
@endpush

@endsection


