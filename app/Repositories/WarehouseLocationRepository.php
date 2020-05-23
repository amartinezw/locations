<?php

namespace App\Repositories;

use App\Category;
use App\Warehouse;
use App\Rack;
use App\WarehouseLocation;
use function foo\func;
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
    public function mapLocations($warehouse_id, $blocks, $levels, $sides, $portage = null)
    {

        $warehouse = Warehouse::find($warehouse_id);
        if (empty($warehouse)) {
            return ApiResponses::badRequest('La bodega no existe.');
        }

        if (null !== $portage) {
            $newRack = 1;
            $prefix = 'P';
        } else {
            $newRack = WarehouseLocation::where('warehouse_id', $warehouse_id)->max('rack') + 1;
            $prefix = 'R';
        }

        $rack = new Rack;
        $rack->name = $newRack;
        $rack->warehouse_id = $warehouse_id;
        $rack->save();

        for ($l=1; $l <= $levels; $l++) {
            for ($b=1; $b <= $blocks; $b++) {
                $warehouseLocation = new WarehouseLocation;
                $warehouseLocation->warehouse_id = $warehouse_id;
                $warehouseLocation->rack_id = $rack->id;
                $warehouseLocation->block = $b;
                $warehouseLocation->level = $l;
                $warehouseLocation->rack = $newRack;
                $warehouseLocation->side = 1;
                $warehouseLocation->mapped_string = $prefix.$newRack.'-A'.$b.'-N'.$l;
                $warehouseLocation->save();
                if ($sides == 2) {
                    $warehouseLocation = new WarehouseLocation;
                    $warehouseLocation->warehouse_id = $warehouse_id;
                    $warehouseLocation->rack_id = $rack->id;
                    $warehouseLocation->block = $b;
                    $warehouseLocation->level = $l;
                    $warehouseLocation->rack = $newRack;
                    $warehouseLocation->side = 2;
                    $warehouseLocation->mapped_string = $prefix.$newRack.'-B'.$b.'-N'.$l;
                    $warehouseLocation->save();
                }
            }
        }

        return ApiResponses::created();
    }
    
    public function insertBlocks($rack_id)
    {
        $rack = Rack::find($rack_id);
        if (empty($rack)) {
            return ApiResponses::badRequest('El rack no existe.');
        }
        $newBlock = WarehouseLocation::where('rack_id', $rack_id)->max('block') + 1;
        $levels = WarehouseLocation::where('rack_id', $rack_id)->max('level');
        for ($i=1; $i <= $levels; $i++) {
            $r = $rack->name < 10 ? 'R0'.$rack->name : 'R'.$rack->name;
            $b = $newBlock < 10 ? 'B0'.$newBlock : 'B'.$newBlock;
            $n = $i < 10 ? 'N0'.$i : 'N'.$i;
            $mapped_string = $r.'-'.$b.'-'.$n;
            $warehouseLocation = new WarehouseLocation;
            $warehouseLocation->warehouse_id = $rack->warehouse_id;
            $warehouseLocation->rack_id = $rack->id;
            $warehouseLocation->block = $newBlock;
            $warehouseLocation->level = $i;
            $warehouseLocation->rack = $rack->name;
            $warehouseLocation->side = 1;
            $warehouseLocation->mapped_string = $mapped_string;
            $warehouseLocation->save();
        }

        return ApiResponses::created();
    }

    public function getlocations(Request $request)
    {
        $column   = 'warehouse_id';
        $order  = 'asc';
        if ($request->column!='undefined' && !is_null($request->column)) {
            $column = $request->column;
            $order  = $request->order;
        }
        if ($request->q) {
            $warehouserepo = WarehouseLocation::with('warehouse', 'warehouse.store')->where('warehouse_id', $request->warehouse_id)->where('mapped_string', 'LIKE', '%'.$request->q.'%')->orderBy($column, $order)->paginate($request->per_page);
        } else {
            $warehouserepo = WarehouseLocation::with('warehouse', 'warehouse.store')->where('warehouse_id', $request->warehouse_id)->orderBy($column, $order)->paginate($request->per_page);
        }

        return ApiResponses::okObject($warehouserepo);
    }

    public function editLocationActive(Request $request)
    {
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

        try {
            $where = [];
            $whereCategory = [];
            $whereSku = [];
            if (!$request->has('withzeros')) {
                $whereSku[] = ['stock', '>', 0];
            }
            $onlyParent = false;
            if ($request->has('product') || $request->has('sku') || $request->has('active') || $request->has('category')) {
                if ($request->has('product')) {
                    $where[] = ['name', 'LIKE', '%'.$request->product.'%'];
                }
                if ($request->has('sku') && strlen($request->sku) < 9) {
                    $whereSku[] = ['sku', 'LIKE', '%'.$request->sku.'%'];
                }
                if ($request->has('sku') && strlen($request->sku) > 8) {
                    $where[] = ['internal_reference', '=', $request->sku];
                }
                if ($request->has('active') && $request->active >= 0) {
                    $where[] = ['activation_disabled', '=', $request->active];
                }
                if ($request->has('category') && $request->category > 0) {
                    if (!$request->subcategory > 0) {
                        $onlyParent = true;
                    } else {
                        $onlyParent = false;
                    }
                    if ($request->has('category') && $request->category > 0 && !$request->subcategory > 0) {
                        $whereCategory[] = ['id', '=', $request->category];
                    } else {
                        $whereCategory[] = ['id', '=', $request->subcategory];
                    }
                }
            }

            $racks = Rack::select('id', 'name as rack')->with(['items' => function ($q) use ($where, $whereCategory, $onlyParent, $whereSku) {
                $q->select('product_id')->whereHas('product', function ($q) use ($where, $whereCategory, $onlyParent, $whereSku) {
                    $q->where($where);
                    $q->whereHas('parentCategory', function ($q) use ($whereCategory, $onlyParent) {
                        if ($onlyParent === true) {
                            $q->whereHas('category', function ($q) use ($whereCategory) {
                                $q->whereHas('parent', function ($q) use ($whereCategory) {
                                    $q->where($whereCategory);
                                });
                            });
                        } else {
                            $q->whereHas('category', function ($q) use ($whereCategory) {
                                $q->where($whereCategory);
                            });
                        }
                    });
                    $q->whereHas('variations', function ($q) use ($whereSku) {
                        $q->where($whereSku);
                    });
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
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getblocks(Request $request)
    {
        $where = [];
        $whereCategory = [];
        $onlyParent = false;
        $whereSku = [];
        if (!$request->has('withzeros')) {
            $whereSku[] = ['stock', '>', 0];
        }
        if ($request->has('product') || $request->has('sku') || $request->has('active') || $request->has('category')) {
            if ($request->has('product')) {
                $where[] = ['name', 'LIKE', '%'.$request->product.'%'];
            }
            if ($request->has('sku') && strlen($request->sku) < 9) {
                $whereSku[] = ['sku', 'LIKE', '%'.$request->sku.'%'];
            }
            if ($request->has('sku') && strlen($request->sku) > 8) {
                $where[] = ['internal_reference', '=', $request->sku];
            }
            if ($request->has('active') && $request->active >= 0) {
                $where[] = ['activation_disabled', '=', $request->active];
            }
            if ($request->has('category') && $request->category > 0) {
                if (!$request->subcategory > 0) {
                    $onlyParent = true;
                } else {
                    $onlyParent = false;
                }
                if ($request->has('category') && $request->category > 0 && !( $request->subcategory > 0 )) {
                    $whereCategory[] = ['id', '=', $request->category];
                } else {
                    $whereCategory[] = ['id', '=', $request->subcategory];
                }
            }
        }
        $blocks = WarehouseLocation::select('id', 'rack', 'block', 'level', 'side', 'mapped_string', 'active')

            ->withCount(['items' => function ($q) use ($where, $whereCategory, $onlyParent, $whereSku) {
                $q->whereHas('product', function ($q) use ($where, $whereCategory, $onlyParent, $whereSku) {
                    $q->where($where);
                    $q->whereHas('parentCategory', function ($q) use ($whereCategory, $onlyParent) {
                        if ($onlyParent === true) {
                            $q->whereHas('category', function ($q) use ($whereCategory) {
                                $q->whereHas('parent', function ($q) use ($whereCategory) {
                                    $q->where($whereCategory);
                                });
                            });
                        } else {
                            $q->whereHas('category', function ($q) use ($whereCategory) {
                                $q->where($whereCategory);
                            });
                        }
                    });
                });
                $q->whereHas('variation', function ($q) use ($whereSku) {
                    $q->where($whereSku);
                });
            }])
            ->where('rack', $request->rack)
            ->where('warehouse_id', $request->warehouse_id)
            ->orderBy('block', 'ASC')
            ->orderBy('level', 'ASC')
            ->get();
        return ApiResponses::okObject($blocks);
    }
}
