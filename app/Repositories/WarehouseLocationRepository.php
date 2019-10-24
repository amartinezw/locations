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

    /**
     * @param $warehouse_id
     * @param $blocks
     * @param $levels
     * @param $sides
     * @param WarehouseLocation $warehouseLocation
     * @return \Illuminate\Http\Response
     */
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
        if($request->column!='undefined' && !is_null($request->column)){
            $column     = $request->column;
            $direction  = $request->direction;
        }
        if($request->q)
            $warehouserepo = WarehouseLocation::with('warehouse', 'warehouse.store')->where('warehouse_id', $request->warehouse_id)->where('mapped_string','LIKE','%'.$request->q.'%')->orderBy($column,$direction)->paginate($request->per_page);
        else
            $warehouserepo = WarehouseLocation::with('warehouse', 'warehouse.store')->where('warehouse_id', $request->warehouse_id)->orderBy($column,$direction)->paginate($request->per_page);

        return ApiResponses::okObject($warehouserepo);
    }

    public function editLocationActive(Request $request){
        $warehouseLocation = WarehouseLocation::find($request->id);
        if (empty($warehouseLocation)) {
            return ApiResponses::badRequest('La localización no existe.');
        }

        $warehouseLocation->active = $request->chk=="true"?1:0;
        $warehouseLocation->save();

        return ApiResponses::created("Se modifico con exito");
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
