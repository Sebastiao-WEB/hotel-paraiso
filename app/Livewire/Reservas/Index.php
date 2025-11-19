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


