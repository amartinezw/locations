<?php

namespace App\Repositories;

use App\Http\Controllers\ApiResponses;
use App\LocationVariation;
use App\Product;
use App\Warehouse;
use App\Store;
use App\Variation;
use App\WarehouseLocation;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class LocationVariationRepository extends BaseRepository
{
    protected $model = 'App\LocationVariation';
    
    public function getSummary(Request $request) {
        $productsLocatedCount = LocationVariation::groupBy('product_id')->get()->count();
        $warehousesCount = Warehouse::get()->count();
        $storesCount = Store::get()->count();
        $warehouseLocationsCount = WarehouseLocation::get()->count();

        $responseObject = [
            'productsLocatedCount' => $productsLocatedCount,
            'warehousesCount' => $warehousesCount,
            'storesCount' => $storesCount,
            'warehouseLocationsCount' => $warehouseLocationsCount
        ];
        return ApiResponses::okObject($responseObject);
    }

    public function getall(Request $request)
    {        
         $where = [];
         $whereHas = [];
        if ($request->has('name') || $request->has('sku') || $request->has('active') || $request->has('department')) {
            if ($request->has('name')) {
                $where[] = ['name', 'LIKE', '%'.$request->name.'%'];
            }
            if ($request->has('active')) {
                $where[] = ['activation_disabled', '=', $request->active];
            }
            if ($request->has('department')) {
                $where[] = ['department', '=', $request->department];
            }
        }
        if ($request->has('notLocated')) {
            $responseArray = Product::with(
                ['variations' => function($q) {
                    $q->select('id','product_id','name','sku', 'stock', 'price');
                },                 
                 'images' => function($q) {
                     $q->select('id','file', 'product_id')->groupBy('product_id');
                 }])
            ->select('id','name','internal_reference', 'provider', 'colors_es')
            ->where($where)            
            ->whereHas('variations', function($q) use ($request) {
                if ($request->has('sku')) {
                    $q->where('sku', $request->sku);
                }
            })                
            ->paginate($request->per_page ?: 20)->toArray();
        } else {
            $responseArray = Product::with(
                ['variations' => function($q) {
                    $q->select('id','product_id','name','sku', 'stock', 'price')
                        ->whereHas('locations');
                },
                 'locations' => function($q) {
                    $q->select('id', 'warehouselocation_id', 'product_id')->groupBy('warehouselocation_id','product_id');
                 },
                 'locations.warehouselocation:id,mapped_string',
                 'variations.locations:id,variation_id,warehouselocation_id',
                 'variations.locations.warehouselocation:id,mapped_string', 
                 'images' => function($q) {
                     $q->select('id','file', 'product_id')->groupBy('product_id');
                 }])
            ->select('id','name','internal_reference', 'provider', 'colors_es')
            ->where($where)
            ->whereHas('locations')
            ->whereHas('variations', function($q) use ($request) {
                if ($request->has('sku')) {
                    $q->where('sku', $request->sku);
                }
            })                
            ->paginate($request->per_page ?: 20)->toArray();            
        }
        
        return ApiResponses::okObject($responseArray);
    }

    public function getItemsInLocation(Request $request)
    {
        $warehouselocation = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string,
            'warehouse_id'  => $request->warehouse_id
        ])->first();

        if (empty($warehouselocation)) {
            return ApiResponses::notFound('No se encontro la ubicacion destino.');
        }
     
        $responseArray = Product::with(
            ['variations' => function($q) use ($warehouselocation) {
                $q->select('id','product_id','name','sku', 'stock', 'price')
                    ->whereHas('locations', function($q) use ($warehouselocation) {
                        $q->where('warehouselocation_id', $warehouselocation->id);            
                    });
            }, 
             'images' => function($q) {
                 $q->select('id','file', 'product_id')->groupBy('product_id');
             }])
        ->select('id','name','internal_reference', 'provider', 'colors_es')
        ->whereHas('locations', function($q) use ($warehouselocation) {
            $q->where('warehouselocation_id', $warehouselocation->id);
        })->paginate($request->per_page ?: 20)->toArray();

        $responseArray = array_merge($responseArray, [
            'warehouselocation_id' => $warehouselocation->id,
            'rack' => $warehouselocation->rack,
            'block' => $warehouselocation->block,
            'level' => $warehouselocation->level,
            'mapped_string' => $warehouselocation->mapped_string
        ]);

        return ApiResponses::okObject($responseArray);
    }

    public function getLocationsOfItem(Request $request)
    {        
        $variation = Variation::where('sku', $request->sku)->first();
        $locationvariations = LocationVariation::with('warehouselocation')
        ->whereHas('variation', function($q) use ($request, $variation) {
            $q->where('product_id', $variation->product_id);
        })
        ->orderBy('warehouselocation_id', 'asc')->get();        

        $locations = [];

        foreach ($locationvariations as $k => $v) {
            $locations[] = $v->warehouselocation->mapped_string;
        }

        $locations = array_values(array_unique($locations));

        return ApiResponses::okObject($locations);
    }

    public function getLocationsOfProduct(Request $request)
    {        
        $product = Product::with(['variations' => function($q) {
            $q->select('id','name','sku', 'product_id');
        }, 'variations.locations' => function($q) {
            $q->select('warehouselocation_id','variation_id');
        }, 'variations.locations.warehouselocation' => function($q) {
            $q->select('id', 'mapped_string');
        },'firstimg:product_id,file'])
            ->select('id','name','internal_reference')
            ->whereHas('variations', function($q) {
                $q->whereHas('locations');
            })->where('id', $request->id)
            ->get();

        return ApiResponses::okObject($product);
    }

    public function getLocationsOfAllProducts(Request $request)
    {        
        $product = Product::with(['variations' => function($q) {
            $q->select('id','name', 'product_id');
        }, 'variations.locations' => function($q) {
            $q->select('warehouselocation_id','variation_id');
        }, 'variations.locations.warehouselocation' => function($q) {
            $q->select('id', 'mapped_string');
        }])
            ->select('id','name','internal_reference')
            ->whereHas('variations', function($q) {
                $q->whereHas('locations');
            })->get();

        return ApiResponses::okObject($product);
    }    

    public function locateItem(Request $request)
    {
        $warehouselocation = WarehouseLocation::where([
            'mapped_string' => $request->mapped_string,
            'warehouse_id'  => $request->warehouse_id
        ])->first();
        if (empty($warehouselocation)) {
            return ApiResponses::notFound('No se encontro la ubicacion destino.');
        }

        if (strlen($request->sku) > 7) {
            $product = Product::where('internal_reference', $request->sku)->first();
            if (empty($product)) {
                return ApiResponses::notFound('No se encontro el estilo a ubicar.');
            }
            $productId = $product->id;
            foreach ($product->variations as $key => $vs) {
                $lvExists = LocationVariation::where([
                    'variation_id' => $vs->id,
                    'warehouselocation_id' => $warehouselocation->id
                ])->first();
                if (empty($lvExists)) {
                    $lv = new LocationVariation;                        
                    $lv->warehouselocation_id = $warehouselocation->id;
                    $lv->variation_id = $vs->id;
                    $lv->product_id = $vs->product_id;
                    $lv->save();
                    $lvCollection[] = $lv->id;
                }
            }
        } else {
            $variation = Variation::where('sku', $request->sku)->first();
            $productId = $variation->product_id;
            if (empty($variation)) {
                return ApiResponses::notFound('No se encontro el SKU a ubicar.');
            } 
            $lvCollection = [];                    
            if ($request->withSiblings == "true") {
                $variationSiblings = Variation::where('product_id', $variation->product_id)->get();
                foreach ($variationSiblings as $key => $vs) {
                    $lvExists = LocationVariation::where([
                        'variation_id' => $vs->id,
                        'warehouselocation_id' => $warehouselocation->id
                    ])->first();
                    if (empty($lvExists)) {
                        $lv = new LocationVariation;                        
                        $lv->warehouselocation_id = $warehouselocation->id;
                        $lv->variation_id = $vs->id;
                        $lv->product_id = $vs->product_id;
                        $lv->save();
                        $lvCollection[] = $lv->id;
                    }
                }
            }

            else if (isset($request->mapped_string) && isset($request->warehouse_id)) {

                $locationVariation = LocationVariation::where([
                    'variation_id' => $variation->id,
                    'warehouselocation_id' => $warehouselocation->id
                ])->first();
                 
                if (empty($locationVariation)) {            
                    $locationVariation = new LocationVariation;                 
                    $locationVariation->warehouselocation_id = $warehouselocation->id;
                    $locationVariation->variation_id = $variation->id;
                    $locationVariation->product_id = $variation->product_id;
                    $locationVariation->save();                    
                } else {
                    return ApiResponses::found('El producto ya se encuentra ubicado aqui');               
                }
            }

        }


        if ($request->withSiblings == "true" || strlen($request->sku) > 7) {
            $responseArray = Product::with(['variations' => function($q) {
                    $q->select('id','name','sku', 'product_id');                          
                }
            ])->select('id','name','internal_reference')
            ->where('id', $productId)->get();
        } else {
            $responseArray = Product::with(['variations' => function($q) use ($variation) {
                $q->select('id','name','sku', 'product_id')->where('id', $variation->id);
            }
            ])->select('id','name','internal_reference')
            ->where('id', $productId)->get();                
        } 
        return ApiResponses::okObject($responseArray);            
    
    }

    public function removeItemFromLocation(Request $request)
    {
        if ($request->sku > 7) {
            $product = Product::where('internal_reference', $request->sku)->first();
            if (empty($product)) {
                return ApiResponses::notFound('No se encontro el estilo.');
            }
            $locationVariationsRemove = LocationVariation::where([
                'product_id' => $product->id,
                'warehouselocation_id' => $request->warehouselocation_id
            ])->delete();
            return ApiResponses::ok();
        } else {
            $variation = Variation::where('sku', $request->sku)->first();
            if (empty($variation)) {
                return ApiResponses::notFound('No se encontro el SKU a remover de ubicacion.');
            }

            if ($request->withSiblings == "true") {
                $variationSiblings = Variation::where('product_id', $variation->product_id)->get();
                foreach ($variationSiblings as $key => $vs) {
                    $lv = LocationVariation::where([
                        'warehouselocation_id' => $request->warehouselocation_id,
                        'variation_id'         => $vs->id
                    ])->first();    
                    if (!empty($lv)) {
                        $lv->delete();
                    }            
                }
                return ApiResponses::ok();
            } else {
                $locationVariation = LocationVariation::where([
                    'warehouselocation_id' => $request->warehouselocation_id,
                    'variation_id'         => $variation->id
                ])->first();
                
                if (empty($locationVariation)) {
                    return ApiResponses::notFound('El producto a eliminar no se encontro en la ubicacion.');
                } else {
                    $responseObject = [
                      'product_id' => $variation->product_id,
                      'deletedSize' => $variation->name,
                    ];                
                    $locationVariation->delete();
                    return ApiResponses::ok($responseObject);
                }            
            }            
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

    public function getLatest(Request $request)
    {
        $responseArray = Product::with(
            ['variations' => function($q) use ($request) {
                $q->select('id','product_id','name','sku')
                    ->whereHas('locations', function($q) use ($request) {
                        $q->whereHas('warehouselocation', function($q) use ($request) {
                            $q->where('warehouse_id', $request->warehouse_id);
                        });            
                    });
            },
             'locations' => function($q) {
                $q->select('id', 'warehouselocation_id', 'product_id')
                    ->groupBy('warehouselocation_id','product_id');
             },
             'locations.warehouselocation:id,mapped_string',
             'variations.locations:id,variation_id,warehouselocation_id,updated_at',
             'variations.locations.warehouselocation:id,mapped_string',  
             'images' => function($q) {
                 $q->select('id','file', 'product_id')->groupBy('product_id');
             }])
        ->select('id','updated_at','name','internal_reference', 'provider', 'colors_es')        
        ->whereHas('locations', function($q) use ($request) {
            $q->whereHas('warehouselocation', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            });
            $q->orderBy('updated_at', 'DESC');
        })->take(10)->get();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageCollection = $responseArray->slice(0, 10)->all();
        $responseArray = new LengthAwarePaginator($currentPageCollection, count($responseArray), 10);

        $responseArray->setPath(LengthAwarePaginator::resolveCurrentPath());

        return ApiResponses::okObject($responseArray);
    }
    
}
