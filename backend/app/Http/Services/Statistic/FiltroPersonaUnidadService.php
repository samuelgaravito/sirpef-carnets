<?php

namespace App\Http\Services\Statistic;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\Ministerio;
use Carbon\Carbon;
use App\Http\Services\Const\ObtenerPersonasService;

class FiltroPersonaUnidadService  
{
    static public function filterPersonasXUnid($unid, $voto_status, $fechaDesde = null, $fechaHasta = null)
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Si $unid es 0, obtener todas las personas
        if ($unid == 0) {
            // Obtener todas las personas sin filtros adicionales
            $personasQuery = ObtenerPersonasService::obtenerPersonas($user);
        } else {
            // Obtener todos los ministerios hijos (herencia) a partir del ID de unidad
            
            $MyUnid = json_decode($user->configUser ->unid_activa, true);
            $todosLosMinisterios = array_map('intval', self::obtenerTodosLosMinisteriosHijos($unid ? $unid : $MyUnid[0]));

            // Actualizar la configuración del usuario
            $user->configUser ->oficina_asignada = json_encode($todosLosMinisterios);
            $user->configUser ->unid_activa = json_encode($todosLosMinisterios);

            // Obtener las personas basadas en los filtros
            $personasQuery = ObtenerPersonasService::obtenerPersonas($user);
        }

        // Aplicar condiciones según el voto_status
        switch ($voto_status) {
            case 0: // No participó (voto false)
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->where('voto', false); // Voto debe ser true
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
                    }
                });
                break;

            case 1: // Sí participó (voto true)
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->where('voto', true); // Voto debe ser true
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
                    }
                });
                break;

                case 2: // Faltantes de participación (sin votos registrados)
                    $personasQuery->whereDoesntHave('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                        if ($fechaDesde && $fechaHasta) {
                            $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                                  ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                        } else {
                            $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
                        }
                    });
                    break;
    

         
            case 3: // Total de participación (con votos registrados)
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
                    }
                });
                break;

           
                case 4: // Sí participó (voto true)
                    $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                        $query->where('voto', null); // Voto debe ser true
                        if ($fechaDesde && $fechaHasta) {
                            $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                                  ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                        } else {
                            $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
                        }
                    });
                    break;

               

            default:
                return response()->json(['message' => 'Estado de voto inválido'], 400);
        }

        $personas = $personasQuery->select(
            'tbl_personas.id', 
            'nombre_completo', 
            'cedula', 
            'correo_electronico', 
            'cargo', 
            'telefono', 
            'tipo_empleado_id' // Agregar este campo
        )->with(['tipoEmpleado', 'registrosEventoActivo']) // Cargar la relación tipoEmpleado y registrosEventoActivo
        ->get();


        // Devolver la información de las personas incluyendo el tipo de empleado y el voto
        return response()->json($personas->map(function ($persona) {
            // Obtener el registro activo, si existe
            $registroActivo = $persona->registrosEventoActivo->first();
            $voto = $registroActivo ? $registroActivo->voto : null;
            $descripcion = $voto == true ? $registroActivo->descripcion : null; // Obtener la descripción si el voto es true
            
            return [
                'id' => $persona->id,
                'nombre_completo' => $persona->nombre_completo,
                'cedula' => $persona->cedula,
                'correo_electronico' => $persona->correo_electronico,
                'cargo' => $persona->cargo,
                'telefono' => $persona->telefono,
                'tipo_empleado_id' => $persona->tipo_empleado_id,
                'tipo_empleado' => $persona->tipoEmpleado ? $persona->tipoEmpleado->tipo : null, // Obtener el tipo de empleado
                'voto' => $voto, // Agregar el voto
                'descripcion' => $descripcion // Agregar la descripción si el voto es true
            ];
        }));
    }

    // Esta función obtiene todos los ministerios hijos recursivamente a partir del ID proporcionado
    static private function obtenerTodosLosMinisteriosHijos($unid)
    {
        // Comenzamos con el ministerio proporcionado
        $ministerioIds = [$unid];
        $hijosPendientes = [$unid];

        // Iterar para obtener todos los ministerios hijos recursivamente
        while (!empty($hijosPendientes)) {
            $hijoId = array_shift($hijosPendientes);
            $hijos = Ministerio::where('ministerio_padre_id', $hijoId)->pluck('id')->toArray();

            $ministerioIds = array_merge($ministerioIds, $hijos);
            $hijosPendientes = array_merge($hijosPendientes, $hijos);
        }

        return $ministerioIds;
    }
}