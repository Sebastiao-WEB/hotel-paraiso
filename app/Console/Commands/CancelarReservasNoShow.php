<?php

namespace App\Console\Commands;

use App\Models\Reserva;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CancelarReservasNoShow extends Command
{
    protected $signature = 'reservas:cancelar-no-show';
    protected $description = 'Cancela reservas pendentes que não foram confirmadas até as 14h do dia de entrada';

    public function handle()
    {
        $hoje = Carbon::today();
        $horaLimite = Carbon::today()->setTime(14, 0, 0);

        // Se ainda não são 14h, não cancela
        if (Carbon::now()->lt($horaLimite)) {
            $this->info('Ainda não são 14h. Nenhuma reserva será cancelada.');
            return;
        }

        $reservas = Reserva::where('status', 'pendente')
            ->whereDate('data_entrada', $hoje)
            ->where('confirmado_em', null)
            ->get();

        $canceladas = 0;

        foreach ($reservas as $reserva) {
            $reserva->update(['status' => 'cancelada']);
            $reserva->quarto->update(['estado' => 'disponivel']);
            $canceladas++;
        }

        $this->info("Canceladas {$canceladas} reservas (no-show).");
        
        return Command::SUCCESS;
    }
}


