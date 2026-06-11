<?php

namespace App\Http\Controllers;

use App\Http\Services\Sva\SvaService;
use Illuminate\Http\JsonResponse;
use Exception;

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
    public function obtenerPorCedulaSVA($cedula): JsonResponse 
    {
        try {
            $data = $this->svaService->getPersonaByCedula($cedula);
            return response()->json($data, 200);
            
        } catch (Exception $e) {
            // El código de excepción captura el status HTTP enviado por el servicio (404, 500, etc.)
            $statusCode = ($e->getCode() >= 400 && $e->getCode() <= 599) ? $e->getCode() : 400;
            
            return response()->json([
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }
}