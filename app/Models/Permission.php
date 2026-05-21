<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    /**
     * Relación Muchos a Muchos con Roles (Mapeo Manual Seguro).
     */
    public function roles(): BelongsToMany
    {
        // Forzamos el uso de la tabla 'permission_role' indicando sus llaves correspondientes
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }
}
