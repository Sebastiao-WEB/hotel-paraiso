<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Quarto;
use App\Models\Reserva;
use App\Models\Stay;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoje = Carbon::today();
        
        // Estatísticas incluindo estadias diretas (walk-in)
        $stats = [
            'total_clientes' => Cliente::count(),
            'total_quartos' => Quarto::count(),
            'quartos_disponiveis' => Quarto::where('estado', 'disponivel')->count(),
            'quartos_ocupados' => Quarto::where('estado', 'ocupado')->count(),
            'reservas_ativas' => Reserva::whereIn('status', ['confirmada', 'checkin'])->count(),
            'stays_ativas' => Stay::where('status', 'active')->count(),
            'reservas_hoje' => Reserva::whereDate('data_entrada', $hoje)
                ->whereIn('status', ['confirmada', 'checkin'])
                ->count(),
            'stays_hoje' => Stay::whereDate('check_in_at', $hoje)
                ->where('status', 'active')
                ->count(),
            'checkouts_hoje' => Reserva::whereDate('data_saida', $hoje)
                ->whereIn('status', ['checkin', 'checkout'])
                ->count() + Stay::whereDate('actual_check_out_at', $hoje)
                ->where('status', 'completed')
                ->count(),
            'receita_mes' => Reserva::whereMonth('created_at', $hoje->month)
                ->whereYear('created_at', $hoje->year)
                ->where('status', 'checkout')
                ->sum('valor_total') + Stay::whereMonth('actual_check_out_at', $hoje->month)
                ->whereYear('actual_check_out_at', $hoje->year)
                ->where('status', 'completed')
                ->sum('total_amount'),
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

        // Gráfico de ocupação (últimos 7 dias) - incluindo estadias diretas
        $ocupacao_diaria = [];
        for ($i = 6; $i >= 0; $i--) {
            $data = $hoje->copy()->subDays($i);
            
            $ocupados_reservas = Quarto::where('estado', 'ocupado')
                ->whereHas('reservas', function($q) use ($data) {
                    $q->whereDate('data_entrada', '<=', $data)
                      ->whereDate('data_saida', '>=', $data)
                      ->whereIn('status', ['checkin', 'checkout']);
                })
                ->count();
            
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

        $proximos_checkouts_stays = Stay::where('status', 'active')
            ->where('expected_check_out_at', '>=', now())
            ->orderBy('expected_check_out_at')
            ->limit(5)
            ->with(['guest', 'room'])
            ->get();

        return view('dashboard', compact('stats', 'proximos_checkins', 'proximos_checkouts', 'proximos_checkouts_stays', 'ocupacao_diaria'));
    }
}


