<?php

namespace App\Repositories;

use App\Warehouse;
use App\WarehouseLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;


class WarehouseLocationRepository extends BaseRepository
{
    protected $model = 'App\WarehouseLocation';


    public function mapLocations($warehouse_id, $blocks, $levels, $sides)
    {

        $warehouse = Warehouse::find($warehouse_id);
        if (empty($warehouse)) {
            return ApiResponses::badRequest('La bodega no existe.');
        }

    	$newRack = WarehouseLocation::where('warehouse_id', $warehouse_id)->max('rack') + 1;


        for ($l=1; $l <= $levels; $l++) {
            for ($b=1; $b <= $blocks; $b++) {
                $warehouseLocation = new WarehouseLocation;
                $warehouseLocation->warehouse_id = $warehouse_id;
                $warehouseLocation->block = $b;
                $warehouseLocation->level = $l;
                $warehouseLocation->rack = $newRack;
                $warehouseLocation->side = 1;
                $warehouseLocation->mapped_string = 'R'.$newRack.'-A'.$b.'-N'.$l;
                $warehouseLocation->save();
                if ($sides == 2) {
                    $warehouseLocation = new WarehouseLocation;
                    $warehouseLocation->warehouse_id = $warehouse_id;
                    $warehouseLocation->block = $b;
                    $warehouseLocation->level = $l;
                    $warehouseLocation->rack = $newRack;
                    $warehouseLocation->side = 2;
                    $warehouseLocation->mapped_string = 'R'.$newRack.'-B'.$b.'-N'.$l;
                    $warehouseLocation->save();
                }
            }
        }

     	return ApiResponses::created();
    }


    public function getlocations(Request $request)
    {
        $column   = 'warehouse_id';
        $direction  = 'asc';
        if($request->column!='undefined'){
            $column     = $request->column;
            $direction  = $request->direction;
        }

        if($request->q)
            $warehouserepo = WarehouseLocation::with('warehouse', 'warehouse.store')->where('warehouse_id', $request->warehouse_id)->where('mapped_string','LIKE','%'.$request->q.'%')->orderBy($column,$direction)->paginate($request->per_page);
        else
            $warehouserepo = WarehouseLocation::with('warehouse', 'warehouse.store')->where('warehouse_id', $request->warehouse_id)->orderBy($column,$direction)->paginate($request->per_page);

        /*$locationvariations = DB::table('variations')
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
            ->groupBy('products.id', 'products.name', 'products.department', 'variations.id', 'variations.sku', 'variations.name')
            ->get();
        var_dump($locationvariations);*/

        return ApiResponses::okObject($warehouserepo);
    }

    public function getalllocations(Request $request)
    {
        $warehouserepo = WarehouseLocation::with('warehouse', 'warehouse.store')->paginate($request->per_page);
        return ApiResponses::okObject($warehouserepo);
    }

    public function getracks(Request $request)
    {
        $warehouse = $request->warehouse_id;
        $racks = WarehouseLocation::select('rack')->distinct()->where('warehouse_id', $warehouse)->paginate($request->per_page);
        if (empty($racks)) {
            return ApiResponses::badRequest('La bodega no existe o no tiene ubicaciones mapeadas.');
        }

        return ApiResponses::okObject($racks);
    }

    public function getblocks(Request $request)
    {
        $blocks = WarehouseLocation::select('id','rack','block','level','side','mapped_string')
                    ->withCount('items')
                    ->where('rack', $request->rack)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->orderBy('side', 'ASC')
                    ->get();
        return ApiResponses::okObject($blocks);
    }

}
