<?php

namespace App\Models;

// Cambiamos la clase base por Authenticatable
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Cliente extends Authenticatable
{
    use Notifiable; // Añadimos notificaciones si las usas

    protected $fillable = ['nombres', 'apellidos', 'correo', 'cargo', 'tipo_usuario', 'password', 'otp_expires_at', 'otp_code'];

    // Ocultar la contraseña en las consultas
    protected $hidden = ['password'];
    
    // Indicarle a Laravel que use 'correo' en lugar de 'email' para loguearse
    public function getAuthIdentifierName()
    {
        return 'correo';
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'cliente_role', 'cliente_id', 'role_id');
    }

    public function tienePermiso(string $permiso): bool
    {
        return $this->roles()->whereHas('permissions', function($query) use ($permiso) {
            $query->where('nombre', $permiso);
        })->exists();
    }

    /**
     * Hace que las notificaciones de Laravel envíen el mensaje a la columna 'correo'
     */
    public function routeNotificationForMail($notification): string
    {
        return $this->correo;
    }
}
    