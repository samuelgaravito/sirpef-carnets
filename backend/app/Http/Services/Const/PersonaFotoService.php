<?php

namespace App\Http\Services\Const;

use App\Models\Persona;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PersonaFotoService
{
    public static function guardarFotoPersona($personaId, $foto, $campo = 'foto')
    {
        // Buscar la persona
        $persona = Persona::findOrFail($personaId);

        // Validar que el archivo sea una imagen
        if (!$foto->isValid()) {
            throw new \Exception('El archivo no es válido');
        }

        // Eliminar foto anterior si existe
        if ($persona->{$campo}) {
            Storage::disk('public')->delete($persona->{$campo});
        }

        // Guardar la imagen en el storage (igual que en el ejemplo)
        $path = $foto->store('personas', 'public');

        // Actualizar el modelo
        $persona->{$campo} = $path;
        $persona->save();

        return $path;
    }

    public static function obtenerFotoPersona($personaId, $campo = 'foto')
    {
        $persona = Persona::findOrFail($personaId);
        
        if (!$persona->{$campo}) {
            return null;
        }
        
        return Storage::disk('public')->url($persona->{$campo});
    }

    public static function eliminarFotoPersona($personaId, $campo = 'foto')
    {
        $persona = Persona::findOrFail($personaId);
        
        if ($persona->{$campo}) {
            Storage::disk('public')->delete($persona->{$campo});
            $persona->{$campo} = null;
            $persona->save();
        }
        
        return true;
    }
}