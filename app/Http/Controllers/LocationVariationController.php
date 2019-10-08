<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiResponses;
use App\Repositories\LocationVariationRepository;

class LocationVariationController extends Controller
{

    protected $categoryService;

    public function __construct(Request $request)
    {                
        $this->locationVariationRepository = new LocationVariationRepository();
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
        
    }

    /**
     * Obtiene la(s) ubicacion(es) de un producto
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLocationsOfItem(Request $request)
    {
        try {
            return $this->locationVariationRepository->getLocationsOfItem($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }  
    }

    /**
     * Ubica el item en la determinada ubicacion utilizando scanner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function locateItemScan(Request $request)
    {
        if (!$request->has('warehouse_id') || !$request->has('sku') || !$request->has('mapped_string')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            $respuesta = $this->locationVariationRepository->locateItem($request);
            return $respuesta;

        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);                        
        }  
    }
    /**
     * Ubica el item en la determinada ubicacion utilizando la web.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function locateItemWeb(Request $request)
    {
        if (!$request->has('warehouse_id') || !$request->has('sku') || !$request->has('mapped_string')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->locateItem($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError();
        }  
    }

    /**
     * Quita el item de la ubicacion determinada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeItemFromLocation(Request $request)
    {
        if (!$request->has('sku') || !$request->has('warehouselocation_id')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->removeItemFromLocation($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError();
        }  
    }

    /**
     * Mueve el item de ubicacion utilizando scanner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function moveItemScan(Request $request)
    {
        if (!$request->has('warehouse_id_to') || !$request->has('warehouse_id_from') || !$request->has('mapped_string_to') || !$request->has('mapped_string_from') || !$request->has('sku')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->moveItem($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError();                   
        }  
    }

    /**
     * Mueve el item de ubicacion utilizando la web.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function moveItemWeb(Request $request)
    {
        if (!$request->has('warehouse_id_to') || !$request->has('warehouse_id_from') || !$request->has('mapped_string_to') || !$request->has('mapped_string_from') || !$request->has('sku')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->moveItem($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError();                     
        }  
    }

    /**
     * Obtiene todas las ubicaciones actuales de los productos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getall(Request $request)
    {
        try {
            return $this->locationVariationRepository->getall($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }                      
    }

    /**
     * Obtiene todas las variaciones de un producto en la ubicacion dada
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getItemsInLocation(Request $request)
    {
        if (!$request->has('warehouse_id') || !$request->has('mapped_string')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->getItemsInLocation($request);

        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
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
