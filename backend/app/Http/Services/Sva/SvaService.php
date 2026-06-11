<?php

namespace App\Http\Services\Sva;

use Illuminate\Support\Facades\Http;
use Exception;

class SvaService
{
    protected $svaUrl;

    public function __construct()
    {
        // Extraemos la URL desde el .env
        $this->svaUrl = env('SVA_SERVER_URL');
    }

    /**
     * Consume la API externa de SVA para buscar una persona por cédula.
     *
     * @param string $cedula
     * @return array
     * @throws Exception
     */
    public function getPersonaByCedula(string $cedula): array
    {
        if (!$this->svaUrl) {
            throw new Exception('La URL del servidor SVA (SVA_SERVER_URL) no está configurada en el .env.', 500);
        }

        // Realizamos la petición HTTP (Ajusta la ruta /api/personas/ si el SVA usa otra)
        $response = Http::get($this->svaUrl . '/api/personas/' . $cedula);

        // Si el servidor externo responde con éxito (200-299)
        if ($response->successful()) {
            return $response->json();
        }

        // Si responde un error controlado (ej. 404 de no encontrado o 403)
        throw new Exception(
            $response->json()['message'] ?? 'No se pudieron obtener los datos de la cédula en el SVA.',
            $response->status()
        );
    }
}