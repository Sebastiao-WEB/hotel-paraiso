<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cargo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function reservasCriadas()
    {
        return $this->hasMany(Reserva::class, 'criado_por');
    }

    public function reservasCheckin()
    {
        return $this->hasMany(Reserva::class, 'checkin_por');
    }

    public function reservasCheckout()
    {
        return $this->hasMany(Reserva::class, 'checkout_por');
    }

    public function isAdmin()
    {
        return $this->cargo === 'admin';
    }

    public function isRecepcionista()
    {
        return $this->cargo === 'recepcionista';
    }

    public function isLimpeza()
    {
        return $this->cargo === 'limpeza';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'cargo'])
            ->logOnlyDirty();
    }
}
