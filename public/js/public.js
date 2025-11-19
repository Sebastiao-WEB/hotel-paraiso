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
        element.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Carregando...';
        element.disabled = true;
    }
}

// Função para esconder loading
function hideLoading(element, originalText) {
    if (element) {
        element.innerHTML = originalText;
        element.disabled = false;
    }
}

// Função para mostrar alerta
function showAlert(message, type = 'success', containerId = 'alert-container') {
    const container = document.getElementById(containerId);
    if (!container) return;
    
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
        return response.data;
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
        // Retornar array diretamente se response.data já for array
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
        return response.data;
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

