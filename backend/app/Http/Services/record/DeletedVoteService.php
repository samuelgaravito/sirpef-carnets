<?php

namespace App\Http\Services\record;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\Registro;
use App\Models\UnidadAdscrita;
use App\Models\Auditoria;



class DeletedVoteService{

    
    static public function DeleteVote($id)
    {
        try {
            // Buscar los registros asociados a la persona
            $registros = Registro::where('persona_id', $id)->get();
    
            if ($registros->isEmpty()) {
                return response()->json(['msg' => 'No se encontraron registros para esta persona'], 404);
            }
    
            // Obtener el nombre de la persona
            $persona = Persona::find($id);
            $personaName = $persona->nombre_completo;
    
            // Eliminar los registros
            Registro::where('persona_id', $id)->delete();
    
            // Registrar auditoria
            $user = auth()->user();
            $unidadActiva = 'Administrador'; // Valor por defecto
    
            if ($user->isAdmin()) {
                $personaUsuario = $user->persona()->first();
    
                if ($personaUsuario && $user->configUser->unid_activa!= 0) {
                    $unidadAdscrita = UnidadAdscrita::where('id', 'LIKE', '%' . $user->configUser->unid_activa . '%')->first();
                    if ($unidadAdscrita) {
                        $unidadActiva = $unidadAdscrita->nombre;
                    }
                }
            } elseif ($user->configUser->unid_activa!= 0) {
                $unidadAdscrita = UnidadAdscrita::where('id', 'LIKE', '%' . $user->configUser->unid_activa . '%')->first();
                if ($unidadAdscrita) {
                    $unidadActiva = $unidadAdscrita->nombre;
                }
            }
    

             // Crear mensaje personalizado

            $message = "La unidad adscrita $unidadActiva eliminó el registro del usuario logeado.";
            
            
            // Llamar a getUserInfo y pasarle el mensaje
            
            $this->getUserInfo($message);

            $auditoria = new Auditoria();
            $auditoria->descripcion = "El usuario {$user->name} de la unidad adscrita {$unidadActiva} eliminó la participación de {$personaName}";
            $auditoria->persona_id = $id;
            $auditoria->save();
    
            return response()->json(['msg' => "Registros eliminados correctamente para la persona {$personaName}"], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }
}