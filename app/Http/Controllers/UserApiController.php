<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Auth;
use Log;
use Validator;


class UserApiController extends Controller
{
    protected $mUser;

    public function __construct()
    {
        $this->mUser = new User();
    }

    /**
     * @param Request $oRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $oRequest)
    {
        // Regresa todos los registros
        try {
            // Verifica las variables para despliegue de datos
            $oValidator = Validator::make($oRequest->all(), [
                'per_page' => 'numeric|between:5,100',
                'order' => 'max:30|in:id,pais_id,nombre,iso_a3,created_at,updated_at,deleted_at',
                'search' => 'max:100',
                'sort' => 'in:asc,desc',
            ]);
            if ($oValidator->fails()) {
                return response()->json(["status" => "fail", "data" => ["errors" => $oValidator->errors()]]);
            }
            // Filtros
            $sFiltro = $oRequest->input('search', false);
            $aUsuarios = $this->mUser
                ->where(
                    function ($q) use ($sFiltro) {
                        if ($sFiltro !== false) {
                            return $q
                                ->orWhere('nombre', 'like', "%$sFiltro%")
                                ->orWhere('email', 'like', "%$sFiltro%");
                        }
                    }
                )
                ->orderBy($oRequest->input('order', 'id'), $oRequest->input('sort', 'asc'))
                ->paginate((int) $oRequest->input('per_page', 25));

            // Envía datos paginados
            return response()->json(["status" => "success", "data" => ["usuarios" => $aUsuarios]]);
        } catch (\Exception $e) {
            // Registra error
            Log::error('Error en '.__METHOD__.' línea '.$e->getLine().':'.$e->getMessage());
            return response()->json([
                'code' => 500,
                'type' => 'Usuario',
                'message' => 'Error al obtener el recurso: '.$e->getMessage(),
            ]);
        }
    }


    /**
     * @param Request $oRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $oRequest)
    {
        try {
            $oValidator = Validator::make($oRequest->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);
            if ($oValidator->fails()) {
                return response()->json(["status" => "fail", "data" => ["errors" => $oValidator->errors()]]);
            }

            $email = $oRequest->email;
            $password = $oRequest->password;
            $credentials = ['email' => $email, 'password' => $password];
            if (Auth::guard('web')->attempt($credentials, false, false)){
                $user = $this->mUser->where('email', $email)->get();
                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'user' => $user,
                ])->setStatusCode(200);
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Usuario o contraseña no encontrados',
                ])->setStatusCode(404);
            }

        } catch (\Exception $e) {
            // Registra error
            Log::error('Error en '.__METHOD__.' línea '.$e->getLine().':'.$e->getMessage());
            return response()->json([
                'code' => 500,
                'type' => 'Usuario',
                'message' => 'Error al obtener el recurso: '.$e->getMessage(),
            ])->setStatusCode(500);
        }
    }

}