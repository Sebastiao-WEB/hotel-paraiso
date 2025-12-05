<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Quarto;
use App\Models\Reserva;
use App\Models\Stay;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\View\Component as ViewComponent;

class Dashboard extends ViewComponent
{
    public function render()
    {
        $hoje = Carbon::today();
        
        // Estatísticas incluindo estadias diretas (walk-in)
        $stats = [
            'total_clientes' => Cliente::count(),
            'total_quartos' => Quarto::count(),
            'quartos_disponiveis' => Quarto::where('estado', 'disponivel')->count(),
            'quartos_ocupados' => Quarto::where('estado', 'ocupado')->count(),
            'reservas_ativas' => Reserva::whereIn('status', ['confirmada', 'checkin'])->count(),
            'stays_ativas' => Stay::where('status', 'active')->count(), // Estadias diretas ativas
            'reservas_hoje' => Reserva::whereDate('data_entrada', $hoje)
                ->whereIn('status', ['confirmada', 'checkin'])
                ->count(),
            'stays_hoje' => Stay::whereDate('check_in_at', $hoje)
                ->where('status', 'active')
                ->count(), // Walk-ins de hoje
            'checkouts_hoje' => Reserva::whereDate('data_saida', $hoje)
                ->whereIn('status', ['checkin', 'checkout'])
                ->count() + Stay::whereDate('actual_check_out_at', $hoje)
                ->where('status', 'completed')
                ->count(), // Check-outs de hoje (reservas + stays)
            'receita_mes' => Reserva::whereMonth('created_at', $hoje->month)
                ->whereYear('created_at', $hoje->year)
                ->where('status', 'checkout')
                ->sum('valor_total') + Stay::whereMonth('actual_check_out_at', $hoje->month)
                ->whereYear('actual_check_out_at', $hoje->year)
                ->where('status', 'completed')
                ->sum('total_amount'), // Receita do mês (reservas + stays)
        ];

        $proximos_checkins = Reserva::where('status', 'confirmada')
            ->whereDate('data_entrada', '>=', $hoje)
            ->orderBy('data_entrada')
            ->limit(5)
            ->with(['cliente', 'quarto'])
            ->get();

        $proximos_checkouts = Reserva::where('status', 'checkin')
            ->whereDate('data_saida', '>=', $hoje)
            ->orderBy('data_saida')
            ->limit(5)
            ->with(['cliente', 'quarto'])
            ->get();

        // Próximos check-outs de estadias diretas
        $proximos_checkouts_stays = Stay::where('status', 'active')
            ->where('expected_check_out_at', '>=', now())
            ->orderBy('expected_check_out_at')
            ->limit(5)
            ->with(['guest', 'room'])
            ->get();

        // Gráfico de ocupação (últimos 7 dias) - incluindo estadias diretas
        $ocupacao_diaria = [];
        for ($i = 6; $i >= 0; $i--) {
            $data = $hoje->copy()->subDays($i);
            
            // Quartos ocupados por reservas
            $ocupados_reservas = Quarto::where('estado', 'ocupado')
                ->whereHas('reservas', function($q) use ($data) {
                    $q->whereDate('data_entrada', '<=', $data)
                      ->whereDate('data_saida', '>=', $data)
                      ->whereIn('status', ['checkin', 'checkout']);
                })
                ->count();
            
            // Quartos ocupados por estadias diretas
            $ocupados_stays = Stay::where('status', 'active')
                ->whereDate('check_in_at', '<=', $data)
                ->whereDate('expected_check_out_at', '>=', $data)
                ->distinct('room_id')
                ->count('room_id');
            
            $ocupacao_diaria[] = [
                'data' => $data->format('d/m'),
                'ocupados' => $ocupados_reservas + $ocupados_stays,
            ];
        }

        return view('livewire.dashboard', compact('stats', 'proximos_checkins', 'proximos_checkouts', 'proximos_checkouts_stays', 'ocupacao_diaria'));
    }
}


