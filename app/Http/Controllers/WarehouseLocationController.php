<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponses;
use App\Warehouse;
use App\WarehouseLocation;
use App\Repositories\WarehouseLocationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;

class WarehouseLocationController extends Controller
{
    protected $warehouseLocationRepository;

    public function __construct(Request $request)
    {
        $this->warehouseLocationRepository = new WarehouseLocationRepository();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Obtener todas las ubicaciones de determinada Bodega.
     *
     * @return \Illuminate\Http\Response
     */
    public function editLocationActive(Request $request)
    {
        try {
            return $this->warehouseLocationRepository->editLocationActive($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    /**
     * Obtener todas las ubicaciones de determinada Bodega.
     *
     * @return \Illuminate\Http\Response
     */
    public function getall(Request $request)
    {
        try {
            return $this->warehouseLocationRepository->getlocations($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    /**
     * Obtener todos los bloques relacionados a un rack y a una bodega.
     *
     * @return \Illuminate\Http\Response
     */
    public function getblocks(Request $request)
    {
        try {
            return $this->warehouseLocationRepository->getblocks($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError();
        }

    }

    /**
     * Obtiene la lista de racks de determinada bodega.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getracks(Request $request)
    {
        try {
            return $this->warehouseLocationRepository->getracks($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError();
        }
    }

    /**
     * Mapea ubicaciones en una bodega (agregar un rack).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function maplocations(Request $request)
    {
        $v = Validator::make($request->all(), $this->warehouseLocationRepository->getRules());
        if ($v->fails()) {
            return responder()->error('field_validation_error', 'Los campos no pasaron la prueba de validacion 3. Verifique sus campos')->respond();
        }
        return $this->warehouseLocationRepository->mapLocations($request->warehouse_id, $request->blocks, $request->levels, $request->sides);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
