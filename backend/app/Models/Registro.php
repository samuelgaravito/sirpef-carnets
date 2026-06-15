<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registro extends Model
{
    protected $table = 'tbl_registros';

    protected $fillable = [
        'voto',
        'descripcion',
        'hora_voto',
        'evento_persona_id',
        'info_carnet_id',
        'foto_carnet',
        'status',
        'emision',
        'imagen_qr',
    ];

    /**
     * Relación: Un registro pertenece a una vinculación EventoPersona.
     * Cambiado a snake_case para hacer match con el Backend/Frontend.
     */
    public function evento_persona(): BelongsTo
    {
        return $this->belongsTo(EventoPersona::class, 'evento_persona_id');
    }

    /**
     * Relación: Un registro pertenece a una configuración de diseño InfoCarnet.
     */
    public function info_carnet(): BelongsTo
    {
        return $this->belongsTo(InfoCarnet::class, 'info_carnet_id');
    }
}
