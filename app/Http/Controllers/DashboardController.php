<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Quarto;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoje = Carbon::today();
        
        $stats = [
            'total_clientes' => Cliente::count(),
            'total_quartos' => Quarto::count(),
            'quartos_disponiveis' => Quarto::where('estado', 'disponivel')->count(),
            'quartos_ocupados' => Quarto::where('estado', 'ocupado')->count(),
            'reservas_ativas' => Reserva::whereIn('status', ['confirmada', 'checkin'])->count(),
            'reservas_hoje' => Reserva::whereDate('data_entrada', $hoje)
                ->whereIn('status', ['confirmada', 'checkin'])
                ->count(),
            'checkouts_hoje' => Reserva::whereDate('data_saida', $hoje)
                ->whereIn('status', ['checkin', 'checkout'])
                ->count(),
            'receita_mes' => Reserva::whereMonth('created_at', $hoje->month)
                ->whereYear('created_at', $hoje->year)
                ->where('status', 'checkout')
                ->sum('valor_total'),
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

        // Gráfico de ocupação (últimos 7 dias)
        $ocupacao_diaria = [];
        for ($i = 6; $i >= 0; $i--) {
            $data = $hoje->copy()->subDays($i);
            $ocupacao_diaria[] = [
                'data' => $data->format('d/m'),
                'ocupados' => Quarto::where('estado', 'ocupado')
                    ->whereHas('reservas', function($q) use ($data) {
                        $q->whereDate('data_entrada', '<=', $data)
                          ->whereDate('data_saida', '>=', $data)
                          ->whereIn('status', ['checkin', 'checkout']);
                    })
                    ->count(),
            ];
        }

        return view('dashboard', compact('stats', 'proximos_checkins', 'proximos_checkouts', 'ocupacao_diaria'));
    }
}


