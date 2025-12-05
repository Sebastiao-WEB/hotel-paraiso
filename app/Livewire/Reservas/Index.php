<?php

namespace App\Livewire\Reservas;

use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Quarto;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFiltro = '';
    public $dataFiltro = '';
    
    // Propriedades para extensão de reserva
    public $mostrarModalExtensao = false;
    public $reservaSelecionada = null;
    public $diasAdicionais = 1;

    protected $queryString = ['search', 'statusFiltro', 'dataFiltro'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmar($id)
    {
        $reserva = Reserva::findOrFail($id);
        
        if ($reserva->status !== 'pendente') {
            session()->flash('error', 'Apenas reservas pendentes podem ser confirmadas!');
            return;
        }

        $reserva->update([
            'status' => 'confirmada',
            'confirmado_em' => now(),
        ]);

        $reserva->quarto->update(['estado' => 'reservado']);

        session()->flash('success', 'Reserva confirmada com sucesso!');
    }

    public function cancelar($id)
    {
        $reserva = Reserva::findOrFail($id);
        
        if (in_array($reserva->status, ['checkout', 'cancelada'])) {
            session()->flash('error', 'Esta reserva não pode ser cancelada!');
            return;
        }

        $reserva->update(['status' => 'cancelada']);
        $reserva->quarto->update(['estado' => 'disponivel']);

        session()->flash('success', 'Reserva cancelada com sucesso!');
    }

    public function delete($id)
    {
        $reserva = Reserva::findOrFail($id);
        
        if (!in_array($reserva->status, ['pendente', 'cancelada'])) {
            session()->flash('error', 'Não é possível excluir esta reserva!');
            return;
        }

        $reserva->delete();
        session()->flash('success', 'Reserva excluída com sucesso!');
    }

    /**
     * Abre o modal para estender uma reserva
     */
    public function abrirModalExtensao($id)
    {
        $this->reservaSelecionada = Reserva::with('quarto')->findOrFail($id);
        
        if ($this->reservaSelecionada->status !== 'checkin') {
            session()->flash('error', 'Apenas reservas em check-in podem ser estendidas!');
            return;
        }
        
        $this->diasAdicionais = 1;
        $this->mostrarModalExtensao = true;
    }

    /**
     * Estende (renova) uma reserva ativa
     */
    public function estender()
    {
        if (!$this->reservaSelecionada) {
            return;
        }

        $this->validate([
            'diasAdicionais' => 'required|integer|min:1|max:30',
        ], [
            'diasAdicionais.required' => 'Informe a quantidade de dias.',
            'diasAdicionais.min' => 'Mínimo de 1 dia.',
            'diasAdicionais.max' => 'Máximo de 30 dias.',
        ]);

        $reserva = $this->reservaSelecionada;
        
        if ($reserva->status !== 'checkin') {
            session()->flash('error', 'Apenas reservas em check-in podem ser estendidas!');
            return;
        }

        $novaDataSaida = Carbon::parse($reserva->data_saida)->addDays($this->diasAdicionais);

        // Verifica se o quarto está disponível para o período adicional
        $quartosOcupados = Reserva::where('quarto_id', $reserva->quarto_id)
            ->where('id', '!=', $reserva->id)
            ->where(function($q) use ($reserva, $novaDataSaida) {
                $q->whereBetween('data_entrada', [$reserva->data_saida, $novaDataSaida])
                  ->orWhereBetween('data_saida', [$reserva->data_saida, $novaDataSaida])
                  ->orWhere(function($q2) use ($reserva, $novaDataSaida) {
                      $q2->where('data_entrada', '<=', $reserva->data_saida)
                         ->where('data_saida', '>=', $novaDataSaida);
                  });
            })
            ->whereIn('status', ['pendente', 'confirmada', 'checkin'])
            ->exists();

        if ($quartosOcupados) {
            session()->flash('error', 'O quarto não está disponível para o período adicional solicitado!');
            return;
        }

        // Calcula o valor adicional
        $valorAdicional = $this->diasAdicionais * $reserva->quarto->preco_diaria;
        $novoValorTotal = $reserva->valor_total + $valorAdicional;

        // Atualiza a reserva
        $reserva->update([
            'data_saida' => $novaDataSaida,
            'valor_total' => $novoValorTotal,
        ]);

        session()->flash('success', "Reserva estendida com sucesso! Adicionados {$this->diasAdicionais} dia(s). Novo valor: R$ " . number_format($novoValorTotal, 2, ',', '.'));
        
        $this->mostrarModalExtensao = false;
        $this->reservaSelecionada = null;
        $this->diasAdicionais = 1;
    }

    public function render()
    {
        $query = Reserva::with(['cliente', 'quarto']);

        if ($this->search) {
            $query->whereHas('cliente', function($q) {
                $q->where('nome', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFiltro) {
            $query->where('status', $this->statusFiltro);
        }

        if ($this->dataFiltro) {
            $query->whereDate('data_entrada', $this->dataFiltro);
        }

        $reservas = $query->orderBy('data_entrada', 'desc')->paginate(10);

        return view('livewire.reservas.index', compact('reservas'));
    }
}


