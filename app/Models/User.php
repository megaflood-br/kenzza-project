<?php

namespace App\Models;

use App\Notifications\WelcomeNotification;
use App\Notifications\KenzzaResetPasswordNotification; // Importação da nova notificação
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Order;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tier',
        'document',
        'phone',
        'zip_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'profile_completed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Rota de Notificação para OneSignal
     */
    public function routeNotificationForOneSignal()
    {
        // Isso diz ao OneSignal: "Envie para o dispositivo que está logado com este ID de usuário"
       return ['include_external_user_ids' => [(string) $this->id]];
    }

    /**
     * SOBRESCREVE A NOTIFICAÇÃO PADRÃO DE RESET DE SENHA (Em inglês)
     * Isso força o sistema a usar a sua notificação KenzzaResetPasswordNotification
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new KenzzaResetPasswordNotification($token));
    }

    /**
     * Accessor para verificar se o perfil está completo
     */
    public function getProfileCompletedAttribute()
    {
        return !empty($this->document) &&
               !empty($this->zip_code) &&
               !empty($this->street) &&
               !empty($this->number);
    }

    /**
     * Gatilhos de Inicialização do Model
     */
    protected static function booted()
    {
        // Limpamos o boot, pois agora o disparo está centralizado no Controller
        // Isso evita disparos duplicados ou erros de fila no momento da criação
    }

    /**
     * SOBRESCREVER O ENVIO PADRÃO DE VERIFICAÇÃO DO LARAVEL
     */
    public function sendEmailVerificationNotification(): void
    {
        // Deixado em branco para silenciar o envio automático do framework.
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
