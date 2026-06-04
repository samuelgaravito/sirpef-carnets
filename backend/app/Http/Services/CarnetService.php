<?php

namespace App\Http\Services;

use App\Models\InfoCarnet;
use Illuminate\Support\Facades\Validator;
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

        return InfoCarnet::create($data);
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
