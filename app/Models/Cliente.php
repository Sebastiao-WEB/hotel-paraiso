<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Cliente extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nome',
        'tipo',
        'email',
        'telefone',
        'nif',
        'endereco',
    ];

    protected $casts = [
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
        return $this->hasMany(Stay::class, 'guest_id');
    }

    public function notasCobranca()
    {
        return $this->hasMany(NotaCobranca::class, 'empresa_id');
    }

    public function isEmpresa()
    {
        return $this->tipo === 'empresa';
    }

    public function isPessoa()
    {
        return $this->tipo === 'pessoa';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nome', 'tipo', 'email', 'telefone', 'nif'])
            ->logOnlyDirty();
    }
}
