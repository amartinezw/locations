<?php

namespace App\Http\Controllers;


use App\Warehouse;
use App\Repositories\WarehouseRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;


class WarehouseController extends Controller
{
    protected $warehouseRepository;

    public function __construct(Request $request)
    {
        $this->warehouseRepository = new WarehouseRepository();
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
        $v = Validator::make($request->all(), $this->warehouseRepository->getRules());
        if ($v->fails()) {
            return ApiResponses::badRequest();
        }
        return $this->warehouseRepository->create(['name' => $request->name, 'store_id' => $request->store_id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function testwh(Request $request)
    {
        return "ware";
    }

    /**
     * Obtiene todas las instancias de bodegas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getall(Request $request)
    {
        try {
            return response()->json($this->warehouseRepository->getall($request), 200);
        } catch (\Exception $e) {
            return responder()->error('fetch_warehouse_error', $e)->respond();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * x
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $v = Validator::make($request->all(), $this->warehouseRepository->getRules());
        if ($v->fails()) {
            return ApiResponses::badRequest("La información recibida no es valida.");
        }
        $warehouse = Warehouse::find($request->warehouse_id);
        $this->warehouseRepository->update($warehouse, ['name' => $request->name, 'store_id' => $request->store_id]);
        return ApiResponses::okObject($warehouse);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $warehouse = Warehouse::find($request->warehouse_id);
        if (empty($warehouse)) {
            return ApiResponses::badRequest('La bodega no existe');
        }
        $this->warehouseRepository->delete($request->warehouse_id);
        return ApiResponses::ok();
    }
}
