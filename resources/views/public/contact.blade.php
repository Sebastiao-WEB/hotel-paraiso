@extends('layouts.public')

@section('title', 'Contato - Hotel Paraíso')

@section('content')
<!-- Hero Section -->
<section class="hero-section d-flex align-items-center" style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920'); min-height: 50vh;">
    <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
    <div class="container hero-content text-white text-center">
        <h1 class="display-4 fw-bold mb-3">Entre em Contato</h1>
        <p class="lead">Estamos aqui para ajudar</p>
    </div>
</section>

<!-- Contato -->
<section class="section-padding">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <h2 class="display-6 fw-bold mb-4">Envie-nos uma Mensagem</h2>
                <div id="alert-container"></div>
                <form id="contact-form" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" class="form-control" required>
                            <div class="invalid-feedback">Por favor, informe seu nome.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                            <div class="invalid-feedback">Por favor, informe um email válido.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assunto *</label>
                            <input type="text" name="assunto" class="form-control" required>
                            <div class="invalid-feedback">Por favor, informe o assunto.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mensagem *</label>
                            <textarea name="mensagem" rows="6" class="form-control" required></textarea>
                            <div class="invalid-feedback">Por favor, escreva sua mensagem.</div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>Enviar Mensagem
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h4 class="mb-4">Informações de Contato</h4>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-geo-alt-fill text-primary fs-4 me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Endereço</h6>
                                    <p class="text-muted mb-0">Av. Principal<br>Nampula, Moçambique</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-telephone-fill text-primary fs-4 me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Telefone</h6>
                                    <p class="text-muted mb-0">+258 84 123 4567</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-envelope-fill text-primary fs-4 me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Email</h6>
                                    <p class="text-muted mb-0">info@hotelparaiso.co.mz</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start">
                                <i class="bi bi-clock-fill text-primary fs-4 me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Horário de Atendimento</h6>
                                    <p class="text-muted mb-0">24 horas por dia<br>7 dias por semana</p>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3">Siga-nos</h6>
                        <div>
                            <a href="#" class="text-primary me-3 fs-4"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="text-primary me-3 fs-4"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="text-primary me-3 fs-4"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="text-primary fs-4"><i class="bi bi-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    
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
            const formData = new FormData(form);
            const dados = {
                nome: formData.get('nome'),
                email: formData.get('email'),
                assunto: formData.get('assunto'),
                mensagem: formData.get('mensagem')
            };
            
            await enviarContato(dados);
            
            showAlert('Mensagem enviada com sucesso! Entraremos em contato em breve.', 'success', 'alert-container');
            form.reset();
            form.classList.remove('was-validated');
            
        } catch (error) {
            const mensagem = error.response?.data?.message || 'Erro ao enviar mensagem. Tente novamente.';
            showAlert(mensagem, 'danger', 'alert-container');
        } finally {
            hideLoading(button, originalText);
        }
    });
});
</script>
@endpush

@endsection


