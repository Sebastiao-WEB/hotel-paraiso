<?php

namespace App\Events;

use App\Models\Stay;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event disparado quando um check-in direto é criado
 * 
 * Usado para auditoria e logs de atividade
 */
class CheckinCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * A estadia criada
     */
    public Stay $stay;

    /**
     * Create a new event instance.
     */
    public function __construct(Stay $stay)
    {
        $this->stay = $stay;
    }
}

