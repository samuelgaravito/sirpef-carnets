<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InfoCarnet extends Model
{
    use HasFactory;

    protected $table = 'tbl_info_carnet';

    protected $fillable = [
        'texto_superior',
        'texto_inferior',
        'sello',
        'firma',
        'imagen_fondo',
        'imagen_pie_pagina',
        'imagen_fondo_reverso',
        'qr_reverso',
        'estatus',
    ];

    /**
     * Relación: Una configuración de carnet puede tener muchos registros emitidos.
     */
    public function registros(): HasMany
    {
        return $this->hasMany(Registro::class, 'info_carnet_id');
    }
}
