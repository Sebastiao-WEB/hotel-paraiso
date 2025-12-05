<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

/**
 * Model Stay - Representa estadias diretas (walk-in check-ins)
 * 
 * Esta entidade armazena check-ins realizados sem reserva prévia.
 * Diferente de Reserva, que requer confirmação prévia, Stay representa
 * entrada imediata de hóspedes.
 */
class Stay extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'guest_id',
        'room_id',
        'check_in_at',
        'expected_check_out_at',
        'actual_check_out_at',
        'status',
        'created_by',
        'checked_out_by',
        'total_amount',
        'payment_type',
        'notes',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'expected_check_out_at' => 'datetime',
        'actual_check_out_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com Cliente (hóspede)
     */
    public function guest()
    {
        return $this->belongsTo(Cliente::class, 'guest_id');
    }

    /**
     * Relacionamento com Quarto
     */
    public function room()
    {
        return $this->belongsTo(Quarto::class, 'room_id');
    }

    /**
     * Relacionamento com User que criou o check-in
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relacionamento com User que realizou o check-out
     */
    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    /**
     * Verifica se a estadia está ativa
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Verifica se a estadia foi completada
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Verifica se a estadia foi cancelada
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Calcula o número de noites da estadia
     */
    public function getNightsAttribute()
    {
        $checkIn = Carbon::parse($this->check_in_at);
        $checkOut = $this->actual_check_out_at 
            ? Carbon::parse($this->actual_check_out_at) 
            : Carbon::parse($this->expected_check_out_at);
        
        return $checkIn->diffInDays($checkOut);
    }

    /**
     * Calcula o valor total baseado nas diárias
     */
    public function calculateTotalAmount()
    {
        $nights = $this->nights;
        $dailyRate = $this->room->preco_diaria;
        return $nights * $dailyRate;
    }

    /**
     * Scope para buscar apenas estadias ativas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para buscar estadias completadas
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Configuração do Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['guest_id', 'room_id', 'check_in_at', 'expected_check_out_at', 'status', 'total_amount'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
