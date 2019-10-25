<?php

namespace App\Repositories;

use App\Warehouse;
use App\Rack;
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

        $rack = new Rack;
        $rack->name = $newRack;
        $rack->save();

        for ($l=1; $l <= $levels; $l++) { 
            for ($b=1; $b <= $blocks; $b++) { 
                $warehouseLocation = new WarehouseLocation;
                $warehouseLocation->warehouse_id = $warehouse_id;
                $warehouseLocation->rack_id = $rack_id;
                $warehouseLocation->block = $b;
                $warehouseLocation->level = $l;
                $warehouseLocation->rack = $newRack;
                $warehouseLocation->side = 1;
                $warehouseLocation->mapped_string = 'R'.$newRack.'-A'.$b.'-N'.$l;
                $warehouseLocation->save();
                if ($sides == 2) {
                    $warehouseLocation = new WarehouseLocation;
                    $warehouseLocation->warehouse_id = $warehouse_id;
                    $warehouseLocation->rack_id = $rack_id;
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
        $warehouserepo = WarehouseLocation::with('warehouse', 'warehouse.store')->where('warehouse_id', $request->warehouse_id)->paginate($request->per_page);
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
        $where = [];
        if ($request->has('name') || $request->has('sku') || $request->has('active') || $request->has('family')) {
            if ($request->has('name')) {
                $where[] = ['name', 'LIKE', '%'.$request->name.'%'];
            }
            if ($request->has('active')) {
                $where[] = ['activation_disabled', '=', $request->active];
            }
            if ($request->has('family')) {
                $where[] = ['family', '=', $request->family];
            }
        }

        $racks = Rack::select('id', 'name as rack')->with(['items' => function($q) use ($where) {            
            $q->select('product_id')->whereHas('product', function($q) use ($where) {
                $q->where($where);
            });
            $q->groupBy('product_id');        
        }])->get();
        foreach ($racks as $rack) {
            $rack->total_items = count($rack->items);
            unset($rack->items);
        }

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
                    ->orderBy('block', 'ASC')
                    ->orderBy('level', 'ASC')
                    ->get();
        return ApiResponses::okObject($blocks);
    }
    
}