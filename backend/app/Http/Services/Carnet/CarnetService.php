<?php

namespace App\Http\Services\Carnet;

use App\Models\InfoCarnet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class CarnetService
{
    public function getAll()
    {
        return InfoCarnet::all();
    }

    public function create(array $data)
    {
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
        if (filter_var($base64Image, FILTER_VALIDATE_URL)) {
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
