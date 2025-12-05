<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Quarto extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'numero',
        'tipo',
        'preco_diaria',
        'estado',
    ];

    protected $casts = [
        'preco_diaria' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * Relacionamento com estadias diretas (walk-in)
     */
    public function stays()
    {
        return $this->hasMany(Stay::class, 'room_id');
    }

    public function isDisponivel()
    {
        return $this->estado === 'disponivel';
    }

    public function isReservado()
    {
        return $this->estado === 'reservado';
    }

    public function isOcupado()
    {
        return $this->estado === 'ocupado';
    }

    public function isEmLimpeza()
    {
        return $this->estado === 'limpeza';
    }

    /**
     * Verifica se o quarto está disponível para um período específico
     * 
     * @param \Carbon\Carbon $dataEntrada
     * @param \Carbon\Carbon $dataSaida
     * @return bool
     */
    public function isDisponivelParaPeriodo($dataEntrada, $dataSaida)
    {
        // Verifica se o quarto está disponível ou em limpeza (pode ser liberado)
        if (!in_array($this->estado, ['disponivel', 'limpeza'])) {
            return false;
        }

        // Verifica se há reservas conflitantes no período
        $reservasConflitantes = \App\Models\Reserva::where('quarto_id', $this->id)
            ->where(function($q) use ($dataEntrada, $dataSaida) {
                $q->whereBetween('data_entrada', [$dataEntrada, $dataSaida])
                  ->orWhereBetween('data_saida', [$dataEntrada, $dataSaida])
                  ->orWhere(function($q2) use ($dataEntrada, $dataSaida) {
                      $q2->where('data_entrada', '<=', $dataEntrada)
                         ->where('data_saida', '>=', $dataSaida);
                  });
            })
            ->whereIn('status', ['pendente', 'confirmada', 'checkin'])
            ->exists();

        return !$reservasConflitantes;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['numero', 'tipo', 'preco_diaria', 'estado'])
            ->logOnlyDirty();
    }
}
