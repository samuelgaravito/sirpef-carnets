<?php

namespace App\Http\Services\Statistic;

use App\Http\Services\Const\ObtenerPersonasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\Ministerio;
use App\Models\Evento;
use App\Models\EventoPersona;

class EstadisticaParticipacionService
{
    static public function GetResumenData($fechaDesde = null, $fechaHasta = null)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Obtén las personas que cumplen con los criterios
        $personasQuery = ObtenerPersonasService::obtenerPersonas();

        // Conteo total de personas
        $countTotal = $personasQuery->count();
        
        // Filtrar por registros de votos
        $totalVotosQuery = clone $personasQuery;
        $totalVotosQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
            if ($fechaDesde && $fechaHasta) {
                $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                      ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
            } else {
                $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
            }
        });

        // Filtrar por votos "true"
        $totalVotoTrueQuery = clone $personasQuery;
        $totalVotoTrueQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
            $query->where('voto', true);
            if ($fechaDesde && $fechaHasta) {
                $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                      ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
            } else {
                $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
            }
        });

        // Filtrar por votos "false"
        $totalVotoFalseQuery = clone $personasQuery;
        $totalVotoFalseQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
            $query->where('voto', false);
            if ($fechaDesde && $fechaHasta) {
                $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                      ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
            } else {
                $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
            }
        });

        // Filtrar por votos "null"
        $totalVotoNullQuery = clone $personasQuery;
        $totalVotoNullQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
            $query->where('voto', null);
            if ($fechaDesde && $fechaHasta) {
                $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                      ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
            } else {
                $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
            }
        });

        // Calcular participantes faltantes
        $totalVotoFaltanteQuery = clone $personasQuery;
        $totalVotoFaltanteQuery->whereDoesntHave('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
            if ($fechaDesde && $fechaHasta) {
                $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                      ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
            } else {
                $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
            }
        });

        // Obtener los resultados
        $totalVotos = $totalVotosQuery->count();
        $totalVotoTrue = $totalVotoTrueQuery->count();
        $totalVotoFalse = $totalVotoFalseQuery->count();
        $totalVotoNull = $totalVotoNullQuery->count();
        $totalVotoFaltante = $totalVotoFaltanteQuery->count();

        // Obtener el tipo del evento activo
        $eventoActivo = EventoPersona::where('evento_id', $user->configUser->evento_activo)->first();
        $tipoEvento = $eventoActivo ? $eventoActivo->tipo : 'empleado'; // Valor predeterminado: 'empleado'

        // Determinar las etiquetas según el tipo de evento
        $etiquetaSi = ($tipoEvento === 'fe-de-vida') ? 'Si Participó' : 'Si Asistió';
        $etiquetaNo = ($tipoEvento === 'fe-de-vida') ? 'No Participó' : 'No Asistió';

        // Retornar los resultados
        return [
            'd' => [$etiquetaSi, $totalVotoTrue, '#004E83'],
            'e' => [$etiquetaNo, $totalVotoFalse, '#c80036'],
            'f' => ['Pendiente', $totalVotoNull, '#4B7EB6'],
            'b' => ['Total de Participación', $totalVotos, '#80B0EC'],
            'c' => ['Participantes faltantes', $totalVotoFaltante, '#1a4968'],
            'a' => ['Total de Personal', $countTotal],
        ];
    }
}