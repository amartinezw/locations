<?php
namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponses;
use Auth;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Log;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Validator;
use DB;


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
                ->with('roles')
                ->orderBy($oRequest->input('order', 'id'), $oRequest->input('sort', 'asc'))
                ->paginate((int) $oRequest->input('per_page', 25));


            // Envía datos paginados
            return response()->json($aUsuarios);
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

    /**
     * @param Request $oRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $oRequest)
    {
        try {

            $oValidator = Validator::make($oRequest->all(), [
                'name' => 'required|min:3',
                'email' => 'required|unique:users,email|email',
                'password' => 'required|min:5',
                'address' => 'required'
            ]);
            if ($oValidator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => 'Validacion fallida '. $oValidator->errors(),
                ])->setStatusCode(422);
            }

            // Crea usuario
            $usuario = $this->mUser->create([
                'name' => $oRequest->name,
                'email' => $oRequest->email,
                'address' => $oRequest->address,
                'email_verified_at' => Carbon::now()->format('Y-m-d H:i'),
                'password' => app('hash')->make($oRequest->password)
            ]);

            $usuario->assignRole($oRequest->rol);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Usuario creado',
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

            //Busca usuario
            $usuario = $this->mUser->find($id);
            if(count($usuario->roles) > 0) {
                $usuario->removeRole($usuario->roles[0]->name);
            }
            $usuario->name = $oRequest->name;
            $usuario->email = $oRequest->email;
            $usuario->address = $oRequest->address;
            $usuario->assignRole($oRequest->roles[0]['name']);
            $usuario->update();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Usuario actualizado',
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
    public function getProfile(Request $oRequest)
    {
        try {            
            $usuario = User::with('roles')->select('id','name','email')->where('id' , $oRequest->user()->id)->first();
            return ApiResponses::okObject($usuario);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
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
            $oUsuario = $this->mUser->find($id);
            if ($oUsuario == null) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Usuario no encontrado',
                ])->setStatusCode(404);
            }

            // Elimina usuario
            $oUsuario->delete();
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Usuario eliminado',
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function checkToken($request)
    {
        $server = new ResourceServer();
        $tokens = new TokenRepository();

        //$psr = (new DiactorosFactory)->createRequest($request);

        try {
            $psr = $server->validateAuthenticatedRequest($request);

            $token = $tokens->find(
                $psr->getAttribute('oauth_access_token_id')
            );

            $currentDate = new DateTime();
            $tokenExpireDate = new DateTime($token->expires_at);

            $isAuthenticated = $tokenExpireDate > $currentDate ? true : false;


            return json_encode(array('authenticated' => $isAuthenticated));

        } catch (OAuthServerException $e) {

            return json_encode(array('error' => 'Something went wrong with authenticating. Please logout and login again.'));
        }
    }

}