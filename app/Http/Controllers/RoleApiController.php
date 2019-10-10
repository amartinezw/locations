<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Log;
use Validator;


class RoleApiController extends Controller
{

    protected $mRole;

    public function __construct()
    {
        $this->mRole = new Role();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $oRequest)
    {
        try {
            // Filtros
            $sFiltro = $oRequest->input('search', false);
            $aRoles = $this->mRole
                ->orderBy($oRequest->input('order', 'id'), $oRequest->input('sort', 'asc'))
                ->paginate((int) $oRequest->input('per_page', 25));

            // Envía datos paginados
            return response()->json($aRoles);
        } catch (\Exception $e) {
            // Registra error
            Log::error('Error en '.__METHOD__.' línea '.$e->getLine().':'.$e->getMessage());
            return response()->json([
                'code' => 500,
                'type' => 'Rol',
                'message' => 'Error al obtener el recurso: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $oRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $oRequest)
    {
        try {
            $oValidator = Validator::make($oRequest->all(), [
                'name' => 'required|min:3|unique:roles,name'
            ]);
            if ($oValidator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => 'Validacion fallida '. $oValidator->errors(),
                ])->setStatusCode(422);
            }

            // Crea usuario
            $this->mRole->create([
                'name' => $oRequest->name
            ]);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Role creado',
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    /**
     * @param Request $oRequest
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $oRequest, $id)
    {
        try {
            $oValidator = Validator::make($oRequest->all(), [
                'id' => 'required|numeric'
            ]);
            if ($oValidator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Validacion fallida '.$oValidator->errors(),
                ])->setStatusCode(500);
            }

            //Busca rol
            $rol = $this->mRole->find($id);
            $rol->name = $oRequest->name;
            $rol->update();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Role actualizado',
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {

            $oValidator = Validator::make(['id' => $id], [
                'id' => 'required|numeric',
            ]);

            if ($oValidator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Validacion fallida',
                ])->setStatusCode(500);
            }

            // Busca usuario
            $oRol = $this->mRole->find($id);
            if ($oRol == null) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Rol no encontrado',
                ])->setStatusCode(404);
            }

            // Elimina rol
            $oRol->delete();
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Rol eliminado',
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

}