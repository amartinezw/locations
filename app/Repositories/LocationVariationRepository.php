<?php

namespace App\Repositories;

use App\LocationVariation;
use App\WarehouseLocation;
use App\Variation;
use Illuminate\Http\Request;
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

        return $locationvariations;
    }

    public function getItemsInLocation(Request $request)
    {
        $warehouselocation = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string,
            'warehouse_id'  => $request->warehouse_id
        ])->firstOrFail();

    	$locationvariations = LocationVariation::with(
        	'variation:id,name,sku,product_id',
        	'variation.product:id,name',
        	'variation.product.images',
        	'warehouselocation:id,mapped_string,warehouse_id'
        )
    	->where('warehouselocation_id', $warehouselocation->id)
    	->paginate($request->per_page);

        return $locationvariations;
    }

    public function locateItem(Request $request)
    {
        $locationVariation = new LocationVariation;
        if (isset($request->warehouselocation_id)) {
            $locationVariation->warehouselocation_id = $request->warehouselocation_id;
        }
        else if (isset($request->mapped_string) && isset($request->warehouse_id)){
            $warehouselocation = WarehouseLocation::where([
                'mapped_string' => $request->mapped_string,
                'warehouse_id'  => $request->warehouse_id
            ])->firstOrFail();
            $locationVariation->warehouselocation_id = $warehouselocation->id;
        }

        $variation = Variation::where('sku', $request->sku)->firstOrFail();
        $locationVariation->variation_id = $variation->id;
        $locationVariation->save();

        return $locationVariation->id;
    }

    public function moveItem(Request $request)
    {
        $warehouselocation_from = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string_from,
            'warehouse_id'  => $request->warehouse_id_from
        ])->firstOrFail();

        $variation = Variation::where('sku', $request->sku)->firstOrFail();

        $locationVariation = LocationVariation::where([
            'warehouselocation_id' => $warehouselocation_from->id,
            'variation_id' => $variation->id
        ])->firstOrFail();

        $warehouselocation_to = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string_to,
            'warehouse_id'  => $request->warehouse_id_to
        ])->firstOrFail();

        $locationVariation->warehouselocation_id = $warehouselocation_to->id;
        $locationVariation->save();

        return $locationVariation->warehouselocation_id;
    }
    
}