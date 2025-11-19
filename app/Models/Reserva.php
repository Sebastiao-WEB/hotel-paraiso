<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class Reserva extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'cliente_id',
        'quarto_id',
        'data_entrada',
        'data_saida',
        'status',
        'valor_total',
        'tipo_pagamento',
        'criado_por',
        'checkin_por',
        'checkout_por',
        'confirmado_em',
        'checkin_em',
        'checkout_em',
    ];

    protected $casts = [
        'data_entrada' => 'date',
        'data_saida' => 'date',
        'valor_total' => 'decimal:2',
        'confirmado_em' => 'datetime',
        'checkin_em' => 'datetime',
        'checkout_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function quarto()
    {
        return $this->belongsTo(Quarto::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function checkinPor()
    {
        return $this->belongsTo(User::class, 'checkin_por');
    }

    public function checkoutPor()
    {
        return $this->belongsTo(User::class, 'checkout_por');
    }

    public function servicos()
    {
        return $this->hasMany(ReservaServico::class);
    }

    public function notaCobranca()
    {
        return $this->hasOne(NotaCobranca::class);
    }

    public function calcularValorTotal()
    {
        $dias = Carbon::parse($this->data_entrada)->diffInDays(Carbon::parse($this->data_saida));
        $valorDiarias = $dias * $this->quarto->preco_diaria;
        $valorServicos = $this->servicos->sum('subtotal');
        return $valorDiarias + $valorServicos;
    }

    public function isPendente()
    {
        return $this->status === 'pendente';
    }

    public function isConfirmada()
    {
        return $this->status === 'confirmada';
    }

    public function isCancelada()
    {
        return $this->status === 'cancelada';
    }

    public function isCheckin()
    {
        return $this->status === 'checkin';
    }

    public function isCheckout()
    {
        return $this->status === 'checkout';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['cliente_id', 'quarto_id', 'data_entrada', 'data_saida', 'status', 'valor_total', 'tipo_pagamento'])
            ->logOnlyDirty();
    }
}
