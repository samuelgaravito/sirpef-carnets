<?php

namespace App\Http\Services\record;
use App\Http\Controllers\ConstController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\Registro;
use App\Models\UnidadAdscrita;
use App\Models\Auditoria;
use App\Models\EventoPersona;


class RegistroAsistenciaService
{
 static public function registerAsistencia(Request $request)
{
    $validatedData = $request->validate([
        'descripcion' => 'nullable',
        'persona_id' => 'required|integer',
    ]);

    $ConstController = new ConstController();
    $personas = $ConstController->obtenerPersonas();

    $persona = Persona::find($validatedData['persona_id']);

    if (!$persona || !$personas->pluck('id')->contains($persona->id)) {
        return response()->json(['msg' => 'La persona no existe o no tiene permiso para registrar un voto'], 404);
    }

    try {
        $eventoPersona = $persona->eventos()->where('evento_id', auth()->user()->configUser->evento_activo)->withPivot('id')->first();

        if (!$eventoPersona) {
            return response()->json(['msg' => 'La persona no está asociada al evento activo'], 404);
        }

        // Obtener los registros de la persona en el día actual usando created_at
        $registrosHoy = Registro::where('evento_persona_id', $eventoPersona->pivot->id)
        ->whereDate('created_at', now()->toDateString()) // Filtrar por fecha usando created_at
        ->count();

        // Validar que no haya más de dos registros por día
        if ($registrosHoy >= 2) {
            return response()->json(['msg' => 'La persona ya completo su asistencia el día hoy.'], 400);
        }

        // Determinar el valor de "vote" basado en el número de registros del día
        $vote = ($registrosHoy === 0) ? true : false;

        // Crear el nuevo registro
        $registro = new Registro;
        $registro->voto = $vote; // Asignar el valor automático
        $registro->descripcion = $validatedData['descripcion'];
        $registro->hora_voto = now()->toTimeString(); // Guardar solo la hora
        $registro->evento_persona_id = $eventoPersona->pivot->id;
        $registro->save();

    } catch (\Throwable $th) {
        return response()->json([$th->getMessage()], 400);
    }

    return response()->json([
        "msg" => "Registrado de asistencia correctamente",
        "vote" => $vote,
        "hora_voto" => $registro->hora_voto,
        "created_at" => $registro->created_at // Opcional: Devolver la fecha y hora de creación
    ], 201);
}
}