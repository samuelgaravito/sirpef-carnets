<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Const\obtenerCedulaServiceFeDeVida;
use App\Http\Services\Const\ObtenerPersonasService;
use App\Http\Services\Const\RegistroAsistenciaPaginacionService;
use App\Http\Services\Const\GetUserInfoService;
use App\Http\Services\Const\checkpdfFeDeVida;
use App\Http\Services\Const\PersonaFotoService;
use Illuminate\Http\Request;
use App\Models\Persona;

class ConstController extends Controller
{
    
     public function obtenerPersonas()
    {
        $user = auth()->user();

      /*   $data = (object) [
            'is_admin' => $user->is_admin,
            "evento_activo" => $user->evento_activo,
            "oficina_asignada" => "[18]",
            "unid_activa" => "[18]"
        ]; */


        return ObtenerPersonasService::obtenerPersonas(/* $data */);  
    }


    public function getUserInfo()
    {
        return GetUserInfoService::getUserInfo();  
    }


    public function obtenerRegistrosAsistencia(Request $request)
    {
        return RegistroAsistenciaPaginacionService::obtenerRegistrosAsistencia($request);  
    }
    

    public static function findByCedula($cedula)
    {
        return ObtenerPersonasService::findByCedula($cedula);  
    }
    

    public function obtenerPorCedulaFeDeVida($cedula)
    {
        return obtenerCedulaServiceFeDeVida::obtenerPorCedulaFeDeVida($cedula);  
    }

    public function checkpdf($cedula)
    {
        return checkpdfFeDeVida::checkpdf($cedula);  
    }

    public function guardarFoto(Request $request, $id)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
    
        try {
            $path = PersonaFotoService::guardarFotoPersona($id, $request->file('foto'));
            
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => PersonaFotoService::obtenerFotoPersona($id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function obtenerFoto($id)
    {
        try {
            $url = PersonaFotoService::obtenerFotoPersona($id);
            
            if (!$url) {
                return response()->json([
                    'success' => false,
                    'message' => 'Foto no encontrada'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la foto'
            ], 500);
        }
    }

}
