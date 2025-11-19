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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['numero', 'tipo', 'preco_diaria', 'estado'])
            ->logOnlyDirty();
    }
}
