<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaServico extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserva_id',
        'servico_id',
        'quantidade',
        'subtotal',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function servico()
    {
        return $this->belongsTo(ServicoExtra::class, 'servico_id');
    }
}
