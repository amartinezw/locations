<?php

namespace App\Repositories;

use App\Warehouse;
use App\WarehouseLocation;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;


class WarehouseLocationRepository extends BaseRepository
{
    protected $model = 'App\WarehouseLocation';


    public function mapLocations($warehouse, $blocks, $levels, $sides)
    {
    	$newRack = WarehouseLocation::where('warehouse_id', $warehouse)->max('rack') + 1;

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
        $warehouserepo = WarehouseLocation::with('warehouse', 'warehouse.store')->paginate($request->per_page);
        return $warehouserepo;
    }

    public function getracks(Request $request)
    {        
        $warehouse = $request->warehouse_id;
        $racks = WarehouseLocation::select('rack')->distinct()->where('warehouse_id', $warehouse)->paginate($request->per_page);
        return $racks;
    }

    public function getblocks(Request $request)
    {                        
        $blocks = WarehouseLocation::select('id','rack','block','level','side','mapped_string')
                    ->withCount('items')
                    ->where('rack', $request->rack)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->get();
        return $blocks;
    }
    
}