<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\LocationVariationRepository;

class LocationVariationController extends Controller
{

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
     * Ubica el item en la determinada ubicacion utilizando scanner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function locateItemScan(Request $request)
    {
        try {
            return response()->json($this->locationVariationRepository->locateItem($request), 200);

        } catch (\Exception $e) {
            return responder()->error('locate_item_error', $e)->respond();                        
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
        try {
            return response()->json($this->locationVariationRepository->locateItem($request), 200);

        } catch (\Exception $e) {
            return responder()->error('locate_item_error', $e)->respond();                        
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
        try {
            return response()->json($this->locationVariationRepository->moveItem($request), 200);

        } catch (\Exception $e) {
            return responder()->error('locate_item_error', $e)->respond();                        
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
        try {
            return response()->json($this->locationVariationRepository->moveItem($request), 200);

        } catch (\Exception $e) {
            return responder()->error('locate_item_error', $e)->respond();                        
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
            return response()->json($this->locationVariationRepository->getall($request), 200);

        } catch (\Exception $e) {
            return responder()->error('fetch_warehouse_error', $e)->respond();                        
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
        try {
            return response()->json($this->locationVariationRepository->getItemsInLocation($request), 200);

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
