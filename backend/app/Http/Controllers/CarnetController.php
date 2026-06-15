<?php

namespace App\Http\Controllers;

use App\Http\Services\Carnet\CarnetService;
use Illuminate\Http\Request;
use Exception;

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

    public function registros()
    {
        $data = $this->carnetService->getAllRegistros();
        return response()->json($data);
    }

    public function showRegistro($id)
    {
        $data = $this->carnetService->getCarnetPreviewData($id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try {
            $infoCarnet = $this->carnetService->create($request->all());
            return response()->json($infoCarnet, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
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
