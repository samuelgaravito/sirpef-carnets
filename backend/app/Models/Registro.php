<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function eventoPersona()
    {
        return $this->belongsTo(EventoPersona::class, 'evento_persona_id');
    }

    public function infoCarnet()
    {
        return $this->belongsTo(InfoCarnet::class, 'info_carnet_id');
    }
}
