<?php

namespace App\Http\Services\Carnet;

use App\Models\InfoCarnet;
use App\Models\Persona;
use App\Models\Registro;
use App\Models\EventoPersona;
use App\Models\Ministerio; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon; // Para registrar la fecha de emisión
use Exception;

class CarnetService
{
    public function getAll()
    {
        return InfoCarnet::all();
    }

    public function getAllRegistros()
    {
        return Registro::with(['evento_persona.persona.ministerio'])->latest()->get();
    }

    public function findRegistroById($id)
    {
        return Registro::with(['evento_persona.persona.ministerio', 'info_carnet'])->findOrFail($id);
    }
    
    public function create(array $data)
    {
        // =========================================================================
        // 1. FLUJO: EMISIÓN DE CARNET A UN SOLICITANTE
        // =========================================================================
        if (isset($data['solicitante'])) {
            $validator = Validator::make($data, [
                'solicitante' => 'required|string',
                'cedula' => 'required|string',
                'cargo' => 'required|string',
                'oficina' => 'nullable|string', // Aquí viene el nombre del ministerio seleccionado
                'foto_img' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            return DB::transaction(function () use ($data) {
                
                // 👈 BUSCAMOS EL ID DEL MINISTERIO POR SU NOMBRE
                $ministerioId = null;
                if (!empty($data['oficina'])) {
                    $ministerio = Ministerio::where('nombre', $data['oficina'])->first();
                    $ministerioId = $ministerio ? $ministerio->id : null;
                }

                $persona = Persona::updateOrCreate(
                    ['cedula' => $data['cedula']],
                    [
                        'nombre_completo' => $data['solicitante'],
                        'cargo' => $data['cargo'],
                        'ministerio_id' => $ministerioId, // 👈 GUARDAMOS EL ID ASOCIADO
                    ]
                );

                $eventoPersona = EventoPersona::updateOrCreate(
                    [
                        'persona_id' => $persona->id,
                        'evento_id' => auth()->user()->configUser->evento_activo ?? null
                    ],
                    ['estatus' => 'activo'] 
                );

                // --- LÓGICA DE DESACTIVACIÓN DEL REGISTRO ANTERIOR ---
                $ultimoRegistro = Registro::where('evento_persona_id', $eventoPersona->id)
                    ->where('status', 1)
                    ->latest()
                    ->first();

                if ($ultimoRegistro) {
                    $ultimoRegistro->update([
                        'status' => 0 
                    ]);
                }
                // -----------------------------------------------------

                $lastInfoCarnet = InfoCarnet::where('estatus', true)->latest()->first();
                
                // Procesamos solo la foto del solicitante en disco
                $fotoPath = $this->saveImage($data['foto_img'], 'carnets/fotos');

                // Guardamos el registro de la emisión (Sin QR por ahora)
                return Registro::create([
                    'evento_persona_id' => $eventoPersona->id,
                    'info_carnet_id' => $lastInfoCarnet ? $lastInfoCarnet->id : null,
                    'foto_carnet' => $fotoPath,
                    'emision' => Carbon::now()->toDateString(), // Mantenemos la fecha de emisión
                    'descripcion' => 'Carnet generado para ' . $persona->nombre_completo,
                    'status' => 1 
                ]);
            });
        }

        // =========================================================================
        // 2. FLUJO: CREACIÓN DE CONFIGURACIÓN GENERAL
        // =========================================================================
        $validator = Validator::make($data, [
            'texto_superior' => 'nullable|string',
            'texto_inferior' => 'nullable|string',
            'sello' => 'required|string',
            'firma' => 'nullable|string',
            'imagen_fondo' => 'required|string',
            'imagen_pie_pagina' => 'required|string',
            'imagen_fondo_reverso' => 'nullable|string',
            'estatus' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        // Blindaje por si el front manda textos vacíos o nulos
        $data['texto_superior'] = !empty($data['texto_superior']) ? $data['texto_superior'] : ' ';
        $data['texto_inferior'] = !empty($data['texto_inferior']) ? $data['texto_inferior'] : ' ';

        // Procesamiento de imágenes base de la plantilla de diseño
        $data['sello'] = $this->saveImage($data['sello'], 'carnets/sellos');
        if (!empty($data['firma'])) {
            $data['firma'] = $this->saveImage($data['firma'], 'carnets/firmas');
        }
        $data['imagen_fondo'] = $this->saveImage($data['imagen_fondo'], 'carnets/fondos');
        $data['imagen_pie_pagina'] = $this->saveImage($data['imagen_pie_pagina'], 'carnets/pie_paginas');

        if (!empty($data['imagen_fondo_reverso'])) {
            $data['imagen_fondo_reverso'] = $this->saveImage($data['imagen_fondo_reverso'], 'carnets/reversos_fondos');
        }

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
