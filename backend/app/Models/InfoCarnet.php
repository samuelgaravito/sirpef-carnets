<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'estatus',
    ];

    public function registros()
    {
        return $this->hasMany(Registro::class, 'info_carnet_id');
    }
}
