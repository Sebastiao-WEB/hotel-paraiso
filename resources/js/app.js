import './bootstrap';
import 'bootstrap';
import axios from 'axios';

// Configurar Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Token CSRF
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Inicializar tooltips e popovers do Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Alterar estado do quarto
    document.querySelectorAll('[data-action="alterar-estado-quarto"]').forEach(function(select) {
        select.addEventListener('change', function() {
            const quartoId = this.dataset.quartoId;
            const novoEstado = this.value;
            
            axios.post(`/admin/quartos/${quartoId}/alterar-estado`, {
                estado: novoEstado
            })
            .then(function(response) {
                if (response.data.success) {
                    location.reload();
                }
            })
            .catch(function(error) {
                alert('Erro ao alterar estado do quarto');
            });
        });
    });

    // Buscar quartos disponíveis ao alterar datas
    const dataEntrada = document.getElementById('data_entrada');
    const dataSaida = document.getElementById('data_saida');
    const quartoSelect = document.getElementById('quarto_id');

    if (dataEntrada && dataSaida && quartoSelect) {
        function atualizarQuartosDisponiveis() {
            if (dataEntrada.value && dataSaida.value) {
                axios.get('/admin/reservas/quartos-disponiveis', {
                    params: {
                        data_entrada: dataEntrada.value,
                        data_saida: dataSaida.value
                    }
                })
                .then(function(response) {
                    quartoSelect.innerHTML = '<option value="">Selecione um quarto</option>';
                    response.data.forEach(function(quarto) {
                        const option = document.createElement('option');
                        option.value = quarto.id;
                        option.textContent = `Quarto ${quarto.numero} - ${quarto.tipo} (MZN ${parseFloat(quarto.preco_diaria).toFixed(2)}/dia)`;
                        quartoSelect.appendChild(option);
                    });
                });
            }
        }

        dataEntrada.addEventListener('change', atualizarQuartosDisponiveis);
        dataSaida.addEventListener('change', atualizarQuartosDisponiveis);
    }

    // Adicionar serviço no check-out
    document.querySelectorAll('[data-action="adicionar-servico"]').forEach(function(button) {
        button.addEventListener('click', function() {
            const reservaId = this.dataset.reservaId;
            const servicoId = this.dataset.servicoId;
            
            axios.post(`/admin/checkin/${reservaId}/adicionar-servico`, {
                servico_id: servicoId,
                quantidade: 1
            })
            .then(function(response) {
                if (response.data.success) {
                    location.reload();
                }
            })
            .catch(function(error) {
                alert(error.response?.data?.error || 'Erro ao adicionar serviço');
            });
        });
    });
});
