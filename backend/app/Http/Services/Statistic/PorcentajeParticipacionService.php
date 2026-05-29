<?php

namespace App\Http\Services\Statistic;

use App\Http\Services\Const\ObtenerPersonasService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\Registro;
use App\Models\EventoPersona;
use App\Models\Ministerio;

class PorcentajeParticipacionService
{
    static public function calcularPorcentajeParticipacion($fechaDesde = null, $fechaHasta = null)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $fechaDesde = $fechaDesde ?? now()->startOfMonth()->toDateString();
        $fechaHasta = $fechaHasta ?? now()->toDateString();

        // 1. Obtener los IDs de ministerios desde oficina_asignada del usuario
        $ministeriosAsignados = json_decode($user->configUser->oficina_asignada, true);

        if (empty($ministeriosAsignados)) {
            return response()->json(['message' => 'El usuario no tiene ministerios asignados'], 404);
        }

        // 2. Obtener las personas que pertenecen a esos ministerios
        $personas = Persona::whereIn('ministerio_id', $ministeriosAsignados)->get();

        if ($personas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron personas en los ministerios asignados'], 404);
        }

        // Obtener los IDs de las personas filtradas
        $personasIds = $personas->pluck('id');

        // 3. Buscar en la tabla tb_evento_persona para el evento activo del usuario
        $eventoId = $user->configUser->evento_activo;
        $personasEnEvento = EventoPersona::whereIn('persona_id', $personasIds)
            ->where('evento_id', $eventoId)
            ->get();

        if ($personasEnEvento->isEmpty()) {
            return response()->json(['message' => 'No se encontraron personas participando en el evento activo con los ministerios asignados'], 404);
        }

        // 4. Obtener los registros de votos en tbl_registros para esas personas
        $eventoPersonaIds = $personasEnEvento->pluck('id'); // IDs de evento_persona

        $votosPorPersona = Registro::selectRaw('evento_persona_id, SUM(CASE WHEN voto THEN 1 ELSE 0 END) as votos_true, SUM(CASE WHEN NOT voto THEN 1 ELSE 0 END) as votos_false')
            ->whereIn('evento_persona_id', $eventoPersonaIds)
            ->whereDate('created_at', '>=', $fechaDesde)
            ->whereDate('created_at', '<=', $fechaHasta)
            ->groupBy('evento_persona_id')
            ->get()
            ->keyBy('evento_persona_id');

        // 5. Agrupar resultados por ministerio y calcular el porcentaje de participación
        $data = $personasEnEvento->groupBy('persona.ministerio_id')->map(function ($personasGrupo, $ministerioId) use ($votosPorPersona) {
            $totalPersonas = $personasGrupo->count();
            $votosTrue = 0;
            $votosFalse = 0;

            foreach ($personasGrupo as $eventoPersona) {
                if (isset($votosPorPersona[$eventoPersona->id])) {
                    $votosTrue += $votosPorPersona[$eventoPersona->id]->votos_true;
                    $votosFalse += $votosPorPersona[$eventoPersona->id]->votos_false;
                }
            }

            $totalVotos = $votosTrue + $votosFalse;
            $porcentajeParticipacion = ($totalVotos * 100) / $totalPersonas;

            // Obtener el nombre del ministerio
            $ministerio = Ministerio::find($ministerioId);

            return  (object)[
                'id' => $ministerioId,
                'name' => $ministerio ? $ministerio->nombre : 'Desconocido',
                'total_personas' => $totalPersonas,
                'total_votos' => $totalVotos,
                'votos_true' => $votosTrue,
                'votos_false' => $votosFalse,
                'porcentaje_participacion' => $porcentajeParticipacion,
            ];
        })->values()->all(); // Convertir a array de objetos y quitar las llaves asociativas

        // Retornar como un array de objetos
        return response()->json($data);
    }
}