<?php

namespace App\Listeners;

use App\Events\CheckinCreated;
use Illuminate\Support\Facades\Log;

/**
 * Listener para registrar atividade de check-in direto
 * 
 * Registra logs detalhados sobre quem fez o check-in, quando e para qual quarto
 */
class LogCheckinActivity
{
    /**
     * Handle the event.
     */
    public function handle(CheckinCreated $event): void
    {
        $stay = $event->stay;

        // Log detalhado da atividade
        Log::info('Check-in direto criado', [
            'stay_id' => $stay->id,
            'guest_id' => $stay->guest_id,
            'guest_name' => $stay->guest->nome,
            'room_id' => $stay->room_id,
            'room_number' => $stay->room->numero,
            'check_in_at' => $stay->check_in_at->toDateTimeString(),
            'expected_check_out_at' => $stay->expected_check_out_at->toDateTimeString(),
            'created_by' => $stay->created_by,
            'created_by_name' => $stay->createdBy->name ?? 'N/A',
            'timestamp' => now()->toDateTimeString(),
        ]);

        // O Spatie Activity Log também registrará automaticamente
        // através do trait LogsActivity no modelo Stay
    }
}

