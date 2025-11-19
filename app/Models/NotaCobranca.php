<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class NotaCobranca extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'reserva_id',
        'empresa_id',
        'valor_total',
        'data_emissao',
        'numero_nota',
        'observacoes',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'data_emissao' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Cliente::class, 'empresa_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['reserva_id', 'empresa_id', 'valor_total', 'data_emissao', 'numero_nota'])
            ->logOnlyDirty();
    }
}
