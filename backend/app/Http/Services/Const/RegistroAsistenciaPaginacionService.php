<?php

namespace App\Http\Services\Const;

use App\Http\Services\Const\ObtenerPersonasService;
use App\Models\Registro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon; // Importar Carbon para manejar fechas

class RegistroAsistenciaPaginacionService
{
    /**
     * Obtiene los registros del evento activo del usuario logueado y los pagina.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    static public function obtenerRegistrosAsistencia(Request $request)
    {
        // Obtener el ID del evento activo del usuario logueado
        $eventoId = Auth::user()->configUser->evento_activo;

        // Obtener las personas asociadas al evento activo usando ObtenerPersonasService
        $personasQuery = ObtenerPersonasService::obtenerPersonas();

        // Obtener los registros asociados al evento activo con los campos requeridos
        $registrosQuery = Registro::join('tb_evento_persona', 'tbl_registros.evento_persona_id', '=', 'tb_evento_persona.id')
            ->join('tbl_personas', 'tb_evento_persona.persona_id', '=', 'tbl_personas.id')
            ->select(
                'tbl_registros.*', // Seleccionar todos los campos de tbl_registros
                'tbl_personas.nombre_completo', // Nombre de la persona
                'tbl_personas.cedula', // Cédula de la persona
                'tbl_personas.ministerio_id', // Ministerio ID de la persona
                'tbl_registros.created_at' // Fecha de creación del registro
            )
            ->where('tb_evento_persona.evento_id', $eventoId)
            ->whereIn('tbl_personas.id', $personasQuery->pluck('id')) // Filtrar por las personas obtenidas
            ->whereDate('tbl_registros.created_at', Carbon::today()); // Filtrar por registros del día de hoy

        // Aplicar filtro de búsqueda si se proporciona
        if ($request->has('search')) {
            $search = $request->input('search');
            $registrosQuery = $registrosQuery->where(function ($query) use ($search) {
                $query->where('tbl_personas.nombre_completo', 'like', "%$search%")
                    ->orWhere('tbl_personas.cedula', 'like', "%$search%")
                    ->orWhere('tbl_registros.descripcion', 'like', "%$search%")
                    ->orWhere('tbl_registros.hora_voto', 'like', "%$search%");
            });
        }

        // Aplicar ordenamiento si se proporciona
        if ($request->has('sort')) {
            $sort = $request->input('sort');
            $direction = $request->input('direction', 'asc'); // Valor predeterminado: 'asc'

            // Validar el campo de ordenamiento
            $allowedSortFields = ['nombre_completo', 'cedula', 'ministerio_id', 'created_at']; // Campos permitidos
            if (!in_array($sort, $allowedSortFields)) {
                $sort = 'created_at'; // Establecer un valor predeterminado
            }

            // Validar la dirección de ordenamiento
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'asc'; // Establecer un valor predeterminado
            }

            $registrosQuery = $registrosQuery->orderBy($sort, $direction);
        }

        // Aplicar paginación
        $perPage = $request->input('per_page', 15); // Número de registros por página
        $registros = $registrosQuery->paginate($perPage);

        // Estructurar la respuesta JSON en el formato requerido
        return response()->json([
            "rows" => [
                "current_page" => $registros->currentPage(),
                "data" => $registros->items(),
                "first_page_url" => $registros->url(1),
                "from" => $registros->firstItem(),
                "last_page" => $registros->lastPage(),
                "last_page_url" => $registros->url($registros->lastPage()),
                "links" => $registros->linkCollection()->toArray(),
                "next_page_url" => $registros->nextPageUrl(),
                "path" => $registros->path(),
                "per_page" => $registros->perPage(),
                "prev_page_url" => $registros->previousPageUrl(),
                "to" => $registros->lastItem(),
                "total" => $registros->total(),
            ]
        ]);
    }
}