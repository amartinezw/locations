<?php

namespace App\Repositories;

use App\Category;
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

    public function getSummary(Request $request)
    {
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
         $whereCategory = [];
         $onlyParent = false;
        if ($request->has('name') || $request->has('sku') || $request->has('active') || $request->has('department')) {
            if ($request->has('name')) {
                $where[] = ['name', 'LIKE', '%'.$request->name.'%'];
            }
            if ($request->has('sku') && strlen($request->sku) > 8) {
                $where[] = ['internal_reference', '=', $request->sku];
            }
            if ($request->active!=-1) {
                $where[] = ['activation_disabled', '=', $request->active];
            }
            if ($request->has('department')) {
                if ($request->department > 0 && !($request->subcategory > 0)) {
                    $onlyParent = true;
                    $whereCategory[] = ['id', '=', $request->department];
                } elseif ($request->department > 0 && $request->subcategory > 0) {
                    $onlyParent = false;
                    $whereCategory[] = ['id', '=', $request->subcategory];
                }
            }
        }
        if ($request->has('notLocated')) {
            $responseArray = Product::with(
                ['variations' => function ($q) {
                    $q->select('id', 'product_id', 'name', 'sku', 'stock', 'price', 'color_id');
                },
                'locations' => function ($q) {
                    $q->select('id', 'warehouselocation_id', 'product_id')->groupBy('warehouselocation_id', 'product_id');
                },
                 'locations.warehouselocation:id,mapped_string',
                 'variations.color:id,name',
                 'images' => function ($q) {
                     $q->select('id', 'file', 'product_id')->groupBy('product_id');
                 }]
            )
            ->select('id', 'name', 'internal_reference', 'provider', 'colors_es', 'family', 'parent_name')
            ->where($where)
            ->whereHas('parentCategory', function ($q) use ($whereCategory, $onlyParent) {
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
            })
            ->whereHas('variations', function ($q) use ($request) {
                if ($request->has('sku') && strlen($request->sku) < 9) {
                    $q->where('sku', $request->sku);
                }
            })
            ->paginate($request->per_page ?: 20)->toArray();
        } else {
            $responseArray = Product::with(
                ['variations' => function ($q) {
                    $q->select('id', 'product_id', 'name', 'sku', 'stock', 'price', 'color_id')
                        ->whereHas('locations');
                },
                 'locations' => function ($q) {
                    $q->select('id', 'warehouselocation_id', 'product_id')->groupBy('warehouselocation_id', 'product_id');
                 },
                 'locations.warehouselocation:id,mapped_string',
                 'variations.color:id,name',
                 'variations.locations:id,variation_id,warehouselocation_id',
                 'variations.locations.warehouselocation:id,mapped_string',
                 'images' => function ($q) {
                     $q->select('id', 'file', 'product_id')->groupBy('product_id');
                 }]
            )
            ->select('id', 'name', 'internal_reference', 'provider', 'colors_es', 'family', 'parent_name')
            ->where($where)
            ->whereHas('parentCategory', function ($q) use ($whereCategory, $onlyParent) {
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
            })
            ->whereHas('locations')
            ->whereHas('variations', function ($q) use ($request) {
                if ($request->has('sku') && strlen($request->sku) < 9) {
                    $q->where('sku', $request->sku);
                }
            })->paginate($request->per_page ?: 20)->toArray();
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
        $whereSku = [];

        $responseArray = Product::with(
            ['variations' => function ($q) use ($warehouselocation, $whereSku) {
                $q->select('id', 'product_id', 'name', 'sku', 'stock', 'price', 'color_id')
                    ->whereHas('locations', function ($q) use ($warehouselocation) {
                        $q->where('warehouselocation_id', $warehouselocation->id);
                    })
                    ->where($whereSku);
            },
            'locations' => function ($q) {
                $q->select('id', 'warehouselocation_id', 'product_id')->groupBy('warehouselocation_id', 'product_id');
            },
             'locations.warehouselocation:id,mapped_string',
             'variations.locations:id,variation_id,warehouselocation_id',
             'variations.locations.warehouselocation:id,mapped_string',
             'variations.color:id,name',
             'images' => function ($q) {
                 $q->select('id', 'file', 'product_id')->groupBy('product_id');
             }]
        )
        ->select('id', 'name', 'internal_reference', 'provider', 'colors_es', 'family', 'parent_name')
        ->whereHas('locations', function ($q) use ($warehouselocation, $whereSku) {
            $q->where('warehouselocation_id', $warehouselocation->id);
            $q->whereHas('variation', function ($q) use ($whereSku) {
                $q->where($whereSku);
            });
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
        if ($request->has('skus')) {
            $skuArray = $request->skus;
            $variations = Variation::with(['locations:id,variation_id,warehouselocation_id', 'locations.warehouselocation:id,mapped_string'])->select('id', 'sku')->whereIn('sku', $skuArray)->get();
            $response = [];
            foreach ($variations as $k => $var) {
                $locations = [];
                foreach ($var->locations as $y => $loc) {
                    $locations[] = $loc->warehouselocation->mapped_string;
                }
                $response[] = ["sku" => $var->sku, "locations" => $locations];
            }
            $skus = [];
            $skus = ["skus" => $response];
            return ApiResponses::okObject($skus);
        }
        $variation = Variation::with(['locations:id,variation_id,warehouselocation_id', 'locations.warehouselocation:id,mapped_string'])->where('sku', $request->sku)->first();

        $locations = [$variation->locations->first()['warehouselocation']['mapped_string']];

        return ApiResponses::okObject($locations);
    }

    public function getLocationsOfProduct(Request $request)
    {
        if (is_numeric($request->sku)) {
            $where = [];
            if (strlen($request->sku) > 12) {
                $where = ['internal_reference' => $request->sku];
            } else {
                $variation = Variation::where('sku', $request->sku)->first();
                $where = ['id' => $variation->product_id];
            }
            $product = Product::where($where)
            ->select('id', 'name', 'provider', 'internal_reference', 'family', 'parent_name', 'colors_es')
            ->with([
                'locations' => function ($q) {
                    $q->select('id', 'product_id', 'warehouselocation_id')->groupBy('warehouselocation_id');
                },
                'locations.warehouselocation:id,mapped_string',
                'variations:id,name,sku,stock,product_id',
                'firstimg:id,product_id,file'])
            ->first();
            if (empty($product)) {
                return ApiResponses::notFound('El producto no fue encontrado.');
            }
        }


        return ApiResponses::okObject($product);
    }

    public function getLocationsOfAllProducts(Request $request)
    {
        $product = Product::with(['variations' => function ($q) {
            $q->select('id', 'name', 'product_id');
        }, 'variations.locations' => function ($q) {
            $q->select('warehouselocation_id', 'variation_id');
        }, 'variations.locations.warehouselocation' => function ($q) {
            $q->select('id', 'mapped_string');
        }])
            ->select('id', 'name', 'internal_reference')
            ->whereHas('variations', function ($q) {
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

        if (strlen($request->sku) > 12) {
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
                    $lv->user_id = $request->user()->id;
                    $lv->save();
                    $lvCollection[] = $lv->id;
                }
            }
        } else {
            $variation = Variation::where('sku', $request->sku)->first();
            if (empty($variation)) {
                return ApiResponses::notFound('No se encontro el SKU a ubicar.');
            }
            $productId = $variation->product_id;
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
                        $lv->user_id = $request->user()->id;
                        $lv->save();
                        $lvCollection[] = $lv->id;
                    }
                }
            } elseif (isset($request->mapped_string) && isset($request->warehouse_id)) {
                $lvExists = LocationVariation::where([
                    'variation_id' => $variation->id,
                    'warehouselocation_id' => $warehouselocation->id
                ])->first();
                if (empty($lvExists)) {
                    $locationVariation = new LocationVariation;
                    $locationVariation->warehouselocation_id = $warehouselocation->id;
                    $locationVariation->variation_id = $variation->id;
                    $locationVariation->product_id = $variation->product_id;
                    $locationVariation->user_id = $request->user()->id;
                    $locationVariation->save();
                }
            }
        }


        if ($request->withSiblings == "true" || strlen($request->sku) > 7) {
            $responseArray = Product::with(['variations' => function ($q) {
                    $q->select('id', 'name', 'sku', 'product_id');
            }
            ])->select('id', 'name', 'internal_reference')
            ->where('id', $productId)->get();
        } else {
            $responseArray = Product::with(['variations' => function ($q) use ($variation) {
                $q->select('id', 'name', 'sku', 'product_id')->where('id', $variation->id);
            }
            ])->select('id', 'name', 'internal_reference')
            ->where('id', $productId)->get();
        }
        return ApiResponses::okObject($responseArray);
    }

    public function removeItemFromLocation(Request $request)
    {
        if (strlen($request->sku) > 12) {
            $product = Product::where('internal_reference', $request->sku)->first();
            if (empty($product)) {
                return ApiResponses::notFound('No se encontro el estilo.');
            }
            $locationVariationsRemove = LocationVariation::where([
                'product_id' => $product->id,
                'warehouselocation_id' => $request->warehouselocation_id
            ])->delete();
            if (empty($locationVariationsRemove)) {
                return ApiResponses::notFound('No se encontro el estilo en la ubicacion.');
            }
            $responseObject = [
              'product_id' => $product->id
            ];
            return ApiResponses::okObject($responseObject);
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
                $responseObject = [
                  'product_id' => $variation->product_id
                ];
                return ApiResponses::okObject($responseObject);
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
                    return ApiResponses::okObject($responseObject);
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
            ['variations' => function ($q) use ($request) {
                $q->select('id', 'product_id', 'name', 'sku')
                    ->whereHas('locations', function ($q) use ($request) {
                        $q->whereHas('warehouselocation', function ($q) use ($request) {
                            $q->where('warehouse_id', $request->warehouse_id);
                        });
                    });
            },
             'locations' => function ($q) {
                $q->select('id', 'warehouselocation_id', 'product_id')
                    ->groupBy('warehouselocation_id', 'product_id');
             },
             'locations.warehouselocation:id,mapped_string',
             'variations.locations:id,variation_id,warehouselocation_id,updated_at',
             'variations.locations.warehouselocation:id,mapped_string',
             'images' => function ($q) {
                 $q->select('id', 'file', 'product_id')->groupBy('product_id');
             }]
        )
        ->select('id', 'updated_at', 'name', 'internal_reference', 'provider', 'colors_es')
        ->whereHas('locations', function ($q) use ($request) {
            $q->whereHas('warehouselocation', function ($q) use ($request) {
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
