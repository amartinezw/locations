<?php

namespace App\Repositories;

use App\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WarehouseRepository extends BaseRepository
{
    protected $model = 'App\Warehouse';

 	public function getall(Request $request)
 	{
 		if(isset($request->store_id)) {
            if($request->q)
                $warehouses = Warehouse::where('store_id', $request->store_id)->where('name','LIKE','%'.$request->q.'%')->with('store')->paginate($request->per_page);
            else
                $warehouses = Warehouse::where('store_id', $request->store_id)->with('store')->paginate($request->per_page);
 		} else {
            if($request->q)
                $warehouses = Warehouse::where('name','LIKE','%'.$request->q.'%')->with('store')->paginate($request->per_page);
            else
 	    	    $warehouses = Warehouse::with('store')->paginate($request->per_page);
 		}
 	    return $warehouses;
 	}
}
