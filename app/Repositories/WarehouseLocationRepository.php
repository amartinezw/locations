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
                $warehouseLocation->warehouse_id = $warehouse;
                $warehouseLocation->block = $b;
                $warehouseLocation->level = $l;
                $warehouseLocation->rack = $newRack;
                $warehouseLocation->side = 1;
                $warehouseLocation->mapped_string = 'R'.$newRack.'-A'.$b.'-N'.$l;
                $warehouseLocation->save();
                if ($sides == 2) {
                    $warehouseLocation = new WarehouseLocation;
                    $warehouseLocation->warehouse_id = $warehouse;
                    $warehouseLocation->block = $b;
                    $warehouseLocation->level = $l;
                    $warehouseLocation->rack = $newRack;              
                    $warehouseLocation->side = 2;
                    $warehouseLocation->mapped_string = 'R'.$newRack.'-B'.$b.'-N'.$l;
                    $warehouseLocation->save();
                }               
            }
        }

     	return true;
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