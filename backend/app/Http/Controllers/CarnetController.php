<?php

namespace App\Http\Controllers;

use App\Http\Services\CarnetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarnetController extends Controller
{
    protected $carnetService;

    public function __construct(CarnetService $carnetService)
    {
        $this->carnetService = $carnetService;
    }

    public function index()
    {
        $data = $this->carnetService->getAll();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'texto_superior' => 'required|string',
            'texto_inferior' => 'required|string',
            'sello' => 'required|string',
            'firma' => 'nullable|string',
            'imagen_fondo' => 'required|string',
            'imagen_pie_pagina' => 'required|string',
            'estatus' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $infoCarnet = $this->carnetService->create($request->all());
        return response()->json($infoCarnet, 201);
    }

    public function show($id)
    {
        $infoCarnet = $this->carnetService->findById($id);
        return response()->json($infoCarnet);
    }

    public function update(Request $request, $id)
    {
        $infoCarnet = $this->carnetService->update($id, $request->all());
        return response()->json($infoCarnet);
    }

    public function destroy($id)
    {
        $this->carnetService->delete($id);
        return response()->json(null, 204);
    }
}
