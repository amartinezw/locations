<?php

namespace App\Repositories;

use App\LocationVariation;
use App\WarehouseLocation;
use App\Variation;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;

class LocationVariationRepository extends BaseRepository
{
    protected $model = 'App\LocationVariation';
    
    public function getall(Request $request)
    {        
         $where = [];
        if ($request->has('name') || $request->has('sku') || $request->has('active') || $request->has('department')) {
            if ($request->has('name')) {
                $where[] = ['products.name', 'LIKE', '%'.$request->name.'%'];
            }
            if ($request->has('sku')) {
                $where[] = ['variations.sku', '=', $request->sku];
            }
            if ($request->has('active')) {
                $where[] = ['products.activation_disabled', '=', $request->active];
            }
            if ($request->has('department')) {
                $where[] = ['products.department', '=', $request->department];
            }
        }
        $locationvariations = DB::table('variations')
                            ->distinct()
                            ->leftJoin('products', 'product_id', '=', 'products.id')                            
                            ->rightJoin('location_variations', 'variations.id', '=', 'location_variations.variation_id')
                            ->select('products.id as product_id', 
                                    'variations.id as variation_id', 
                                    'variations.sku', 
                                    'variations.name as variation', 
                                    DB::raw('count(variations.id) as stock'), 
                                    'products.name as product', 
                                    'products.department as department', 
                                    DB::raw('(SELECT images.file FROM images WHERE products.id = images.product_id limit 1) as image'))
                            ->where($where)
                            ->groupBy('products.id', 'products.name', 'products.department', 'variations.id', 'variations.sku', 'variations.name')
                            ->paginate($request->per_page ?: 20 )->toArray();

        return ApiResponses::okObject($locationvariations);
    }

    public function getItemsInLocation(Request $request)
    {
        $warehouselocation = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string,
            'warehouse_id'  => $request->warehouse_id
        ])->first()->toArray();

        if (empty($warehouselocation)) {
            return ApiResponses::notFound('La ubicacion no existe.');
        }
    	$locationvariations = LocationVariation::with(
        	'variation:id,name,sku,product_id',
        	'variation.product:id,name',
        	'variation.product.images'
        )
    	->where('warehouselocation_id', $warehouselocation['id'])
    	->paginate($request->per_page)->toArray();
        $responseArray = array_merge($warehouselocation, $locationvariations);

        return ApiResponses::okObject($responseArray);
    }

    public function getLocationsOfItem(Request $request)
    {        
        $locationvariations = LocationVariation::with('warehouselocation')
        ->whereHas('variation', function($q) use ($request) {
            $q->where('sku', $request->sku);
        })->get();        

        $locations = [];

        foreach ($locationvariations as $k => $v) {
            $locations[] = $v->warehouselocation->mapped_string;
        }

        return ApiResponses::okObject($locations);
    }    

    public function locateItem(Request $request)
    {
        $locationVariation = new LocationVariation;
        if (isset($request->warehouselocation_id)) {
            $warehouselocation = WarehouseLocation::find($request->warehouselocation_id);
            if (empty($warehouselocation)) {
                return ApiResponses::notFound('No se encontro la ubicacion destino.');
            }
            $locationVariation->warehouselocation_id = $request->warehouselocation_id;
        }
        else if (isset($request->mapped_string) && isset($request->warehouse_id)){
            $warehouselocation = WarehouseLocation::where([
                'mapped_string' => $request->mapped_string,
                'warehouse_id'  => $request->warehouse_id
            ])->first();
            if (empty($warehouselocation)) {
                return ApiResponses::notFound('No se encontro la ubicacion destino.');
            }
            $locationVariation->warehouselocation_id = $warehouselocation->id;
        }

        $variation = Variation::where('sku', $request->sku)->first();
        if (empty($variation)) {
            return ApiResponses::notFound('No se encontro el SKU a ubicar.');
        }
        $locationVariation->variation_id = $variation->id;
        $locationVariation->save();
        $responseArray = LocationVariation::with(
            'variation:id,name,sku,product_id',
            'variation.product:id,name',
            'variation.product.images'
        )->where('id', $locationVariation->id)->get();
        return ApiResponses::okObject($responseArray);
    }

    public function removeItemFromLocation(Request $request)
    {
        $variation = Variation::where('sku', $request->sku)->first();
        if (empty($variation)) {
            return ApiResponses::notFound('No se encontro el SKU a remover de ubicacion.');
        }
        $locationVariation = LocationVariation::where([
            'warehouselocation_id' => $request->warehouselocation_id,
            'variation_id'         => $variation->id
        ])->first();
        
        if (empty($locationVariation)) {
            return ApiResponses::notFound('El producto a eliminar no se encontro en la ubicacion.');
        } else {
            $locationVariation->delete();
            return ApiResponses::ok();
        }
    }

    public function moveItem(Request $request)
    {
        $warehouselocation_from = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string_from,
            'warehouse_id'  => $request->warehouse_id_from
        ])->first();

        if (empty($warehouselocation_from)) {
            return ApiResponses::notFound('No se encontro la ubicacion inicial.');
        }

        $variation = Variation::where('sku', $request->sku)->first();

        if (empty($variation)) {
            return ApiResponses::notFound('No se encontro el SKU a ubicar.');
        }

        $locationVariation = LocationVariation::where([
            'warehouselocation_id' => $warehouselocation_from->id,
            'variation_id' => $variation->id
        ])->first();

        if (empty($locationVariation)) {
            return ApiResponses::notFound('El item no se encontraba ubicado ahi.');
        }

        $warehouselocation_to = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string_to,
            'warehouse_id'  => $request->warehouse_id_to
        ])->first();

        if (empty($warehouselocation_to)) {
            return ApiResponses::notFound('No se encontro la ubicacion destino.');
        }

        $locationVariation->warehouselocation_id = $warehouselocation_to->id;
        $locationVariation->save();

        return ApiResponses::okObject($locationVariation);
    }
    
}
