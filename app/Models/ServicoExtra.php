<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ServicoExtra extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nome',
        'preco',
        'descricao',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reservaServicos()
    {
        return $this->hasMany(ReservaServico::class, 'servico_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nome', 'preco', 'descricao'])
            ->logOnlyDirty();
    }
}
