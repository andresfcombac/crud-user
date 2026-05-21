<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    /**
     * Relación Muchos a Muchos con Permisos (Mapeo Manual).
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }

    /**
     * Relación Muchos a Muchos con Clientes (Forzado Manual).
     */
    public function clientes(): BelongsToMany
    {
        // Forzamos el uso de la tabla 'cliente_role' con sus llaves correspondientes
        return $this->belongsToMany(Cliente::class, 'cliente_role', 'role_id', 'cliente_id');
    }
}
