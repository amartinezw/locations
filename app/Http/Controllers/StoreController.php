<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StoreRepository;

class StoreController extends Controller
{

    protected $storeRepository;
    public function __construct(Request $request)
    {
        $this->storeRepository = new StoreRepository();
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
     * Obtener todas las tiendas.
     *
     * @return \Illuminate\Http\Response
     */
    public function getall(Request $request)
    {
        try {
            return response()->json($this->storeRepository->getAll($request), 200);

        } catch (\Exception $e) {
            return responder()->error('fetch_stores_error', $e)->respond();
        }
    }

    /**
     * Obtener todas las tiendas sin paginado.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllForSelect(Request $request)
    {
        try {
            return response()->json($this->storeRepository->getAllForSelect($request), 200);

        } catch (\Exception $e) {
            return responder()->error('fetch_stores_error', $e)->respond();
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
        return "asd";
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
        return "updated";
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
