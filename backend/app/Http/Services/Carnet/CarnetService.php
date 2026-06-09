<?php

namespace App\Http\Services\Carnet;

use App\Models\InfoCarnet;
use App\Models\Persona;
use App\Models\Registro;
use App\Models\EventoPersona;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // <-- Importamos DB para la transacción
use Illuminate\Support\Str;
use Exception;

class CarnetService
{
    public function getAll()
    {
        return InfoCarnet::all();
    }

    public function getAllRegistros()
    {
        return Registro::with(['eventoPersona.persona'])->latest()->get();
    }



    
    public function create(array $data)
    {
        if (isset($data['solicitante'])) {
            $validator = Validator::make($data, [
                'solicitante' => 'required|string',
                'cedula' => 'required|string',
                'cargo' => 'required|string',
                'oficina' => 'nullable|string',
                'foto_img' => 'required|string'
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            // Usamos una transacción para asegurar que los cambios de estatus sean atómicos
            return DB::transaction(function () use ($data) {
                
                $persona = Persona::updateOrCreate(
                    ['cedula' => $data['cedula']],
                    [
                        'nombre_completo' => $data['solicitante'],
                        'cargo' => $data['cargo'],
                    ]
                );

                // Nota: Asegúrate de que el 'estatus' aquí cumpla con los CHECK de Postgres si aplica
                $eventoPersona = EventoPersona::updateOrCreate(
                    [
                        'persona_id' => $persona->id,
                        'evento_id' => auth()->user()->configUser->evento_activo ?? null
                    ],
                    ['estatus' => 'activo'] 
                );

                // --- LÓGICA DE DESACTIVACIÓN DEL REGISTRO ANTERIOR ---
                // Buscamos el último registro que pertenezca a este evento_persona
                $ultimoRegistro = Registro::where('evento_persona_id', $eventoPersona->id)
                    ->latest()
                    ->first();

                // Si existe un registro previo, cambiamos su estatus a false (o 0)
                if ($ultimoRegistro) {
                    $ultimoRegistro->update([
                        'status' => 0 // Cambiar a false o 'inactivo' según el tipo de dato de tu columna 'status'
                    ]);
                }
                // -----------------------------------------------------

                $lastInfoCarnet = InfoCarnet::where('estatus', true)->latest()->first();
                $fotoPath = $this->saveImage($data['foto_img'], 'carnets/fotos');

                return Registro::create([
                    'evento_persona_id' => $eventoPersona->id,
                    'info_carnet_id' => $lastInfoCarnet ? $lastInfoCarnet->id : null,
                    'foto_carnet' => $fotoPath,
                    'descripcion' => 'Carnet generado para ' . $persona->nombre_completo,
                    'status' => 1 // El nuevo carnet nace activo
                ]);
            });
        }

        // Resto del código para la creación de configuraciones InfoCarnet...
        $validator = Validator::make($data, [
            'texto_superior' => 'required|string',
            'texto_inferior' => 'required|string',
            'sello' => 'required|string',
            'firma' => 'nullable|string',
            'imagen_fondo' => 'required|string',
            'imagen_pie_pagina' => 'required|string',
            'estatus' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        $data['sello'] = $this->saveImage($data['sello'], 'carnets/sellos');
        if (!empty($data['firma'])) {
            $data['firma'] = $this->saveImage($data['firma'], 'carnets/firmas');
        }
        $data['imagen_fondo'] = $this->saveImage($data['imagen_fondo'], 'carnets/fondos');
        $data['imagen_pie_pagina'] = $this->saveImage($data['imagen_pie_pagina'], 'carnets/pie_paginas');

        return InfoCarnet::create($data);
    }

    private function saveImage($base64Image, $path)
    {
        if (empty($base64Image)) {
            return null;
        }

        if (filter_var($base64Image, FILTER_VALIDATE_URL)) {
            return $base64Image;
        }

        if (!str_contains($base64Image, ',')) {
            return $base64Image;
        }

        $image_service_str = substr($base64Image, strpos($base64Image, ",") + 1);
        $image = base64_decode($image_service_str);
        $extension = explode('/', explode(':', substr($base64Image, 0, strpos($base64Image, ';')))[1])[1];
        $filename = Str::random(10) . '.' . $extension;
        $fullPath = $path . '/' . $filename;

        Storage::disk('public')->put($fullPath, $image);

        return asset('storage/' . $fullPath);
    }

    public function findById($id)
    {
        return InfoCarnet::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $infoCarnet = $this->findById($id);
        $infoCarnet->update($data);
        return $infoCarnet;
    }

    public function delete($id)
    {
        $infoCarnet = $this->findById($id);
        return $infoCarnet->delete();
    }
}
