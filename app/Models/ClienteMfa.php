<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteMfa extends Model
{
    // Indicamos el nombre exacto de la tabla intermedia creada en DBeaver
    protected $table = 'cliente_mfa';

    protected $fillable = [
        'cliente_id',
        'token',
        'expires_at',
        'estado'
    ];

    /**
     * Relación inversa: Un registro de MFA pertenece a un Cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
