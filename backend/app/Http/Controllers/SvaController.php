<?php

namespace App\Http\Controllers;

use App\Http\Services\Sva\SvaService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;

class SvaController extends Controller
{
    protected $svaService;

    // Inyectamos el servicio en el constructor
    public function __construct(SvaService $svaService)
    {
        $this->svaService = $svaService;
    }

    /**
     * Buscar persona por cédula a través del servicio SVA.
     *
     * @param string $cedula
     * @return JsonResponse
     */
    public function obtenerPorCedulaSVA(Request $request, $cedula): JsonResponse 
    {
        $data = $this->svaService->getPersonaByCedula($request, $cedula);
        return response()->json($data, 200);
    }
}