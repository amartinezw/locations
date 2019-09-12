<?php

namespace App\Repositories;

use App\LocationVariation;
use App\WarehouseLocation;
use App\Variation;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationVariationRepository extends BaseRepository
{
    protected $model = 'App\LocationVariation';
    
    public function getall(Request $request)
    {        
        $locationvariations = LocationVariation::with(
        	'variation:id,name,sku,product_id',
        	'variation.product:id,name',
        	'variation.product.images',
        	'warehouselocation:id,mapped_string,warehouse_id',
        	'warehouselocation.warehouse:id,name,store_id',
        	'warehouselocation.warehouse.store:id,name')
        ->paginate($request->per_page);

        return ApiResponses::okObject($locationvariations);
    }

    public function getItemsInLocation(Request $request)
    {
        $warehouselocation = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string,
            'warehouse_id'  => $request->warehouse_id
        ])->first();

        if (empty($warehouselocation)) {
            return ApiResponses::badRequest('La ubicacion no existe.');
        }

    	$locationvariations = LocationVariation::with(
        	'variation:id,name,sku,product_id',
        	'variation.product:id,name',
        	'variation.product.images',
        	'warehouselocation:id,mapped_string,warehouse_id'
        )
    	->where('warehouselocation_id', $warehouselocation->id)
    	->paginate($request->per_page);

        return ApiResponses::okObject($locationvariations);
    }

    public function locateItem(Request $request)
    {
        $locationVariation = new LocationVariation;
        if (isset($request->warehouselocation_id)) {
            $warehouselocation = WarehouseLocation::find($request->warehouselocation_id);
            if (empty($warehouselocation)) {
                return ApiResponses::badRequest('No se encontro la ubicacion destino.');
            }
            $locationVariation->warehouselocation_id = $request->warehouselocation_id;
        }
        else if (isset($request->mapped_string) && isset($request->warehouse_id)){
            $warehouselocation = WarehouseLocation::where([
                'mapped_string' => $request->mapped_string,
                'warehouse_id'  => $request->warehouse_id
            ])->first();
            if (empty($warehouselocation)) {
                return ApiResponses::badRequest('No se encontro la ubicacion destino.');
            }
            $locationVariation->warehouselocation_id = $warehouselocation->id;
        }

        $variation = Variation::where('sku', $request->sku)->first();
        if (empty($variation)) {
            return ApiResponses::badRequest('No se encontro el SKU a ubicar.');
        }
        $locationVariation->variation_id = $variation->id;
        $locationVariation->save();

        return ApiResponses::okObject($locationVariation);
    }

    public function moveItem(Request $request)
    {
        $warehouselocation_from = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string_from,
            'warehouse_id'  => $request->warehouse_id_from
        ])->first();

        if (empty($warehouselocation_from)) {
            return ApiResponses::badRequest('No se encontro la ubicacion inicial.');
        }

        $variation = Variation::where('sku', $request->sku)->first();

        if (empty($variation)) {
            return ApiResponses::badRequest('No se encontro el SKU a ubicar.');
        }

        $locationVariation = LocationVariation::where([
            'warehouselocation_id' => $warehouselocation_from->id,
            'variation_id' => $variation->id
        ])->first();

        if (empty($locationVariation)) {
            return ApiResponses::badRequest('El item no se encontraba ubicado ahi.');
        }

        $warehouselocation_to = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string_to,
            'warehouse_id'  => $request->warehouse_id_to
        ])->first();

        if (empty($warehouselocation_to)) {
            return ApiResponses::badRequest('No se encontro la ubicacion destino.');
        }

        $locationVariation->warehouselocation_id = $warehouselocation_to->id;
        $locationVariation->save();

        return ApiResponses::okObject($locationVariation);
    }
    
}