<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\LocationVariation;
use App\Repositories\LocationVariationRepository;
use App\Variation;
use App\WarehouseLocation;
use Illuminate\Http\Request;
use \Milon\Barcode\DNS1D;

class LocationVariationController extends Controller
{

    protected $categoryService;

    protected $tags = [
       0   => 'Normal',
       1   => 'Normal',
       2   => 'Rebaja',
       3   => 'Promoción',
       4   => 'Liquidación'
    ];

    public function __construct(Request $request)
    {
        $this->locationVariationRepository = new LocationVariationRepository();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Obtiene la(s) ubicacion(es) de un sku
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLocationsOfItem(Request $request)
    {
        try {
            return $this->locationVariationRepository->getLocationsOfItem($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }

    /**
     * Obtiene la(s) ubicacion(es) de un producto
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLocationsOfProduct(Request $request)
    {
        try {
            return $this->locationVariationRepository->getLocationsOfProduct($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }

    /**
     * Ubica el item en la determinada ubicacion utilizando scanner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function locateItemScan(Request $request)
    {
        if (!$request->has('warehouse_id') || !$request->has('sku') || !$request->has('mapped_string')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            $respuesta = $this->locationVariationRepository->locateItem($request);
            return $respuesta;
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }
    /**
     * Ubica el item en la determinada ubicacion utilizando la web.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function locateItemWeb(Request $request)
    {
        if (!$request->has('warehouse_id') || !$request->has('sku') || !$request->has('mapped_string')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->locateItem($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError();
        }
    }

    /**
     * Quita el item de la ubicacion determinada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeItemFromLocation(Request $request)
    {
        if (!$request->has('sku') || !$request->has('warehouselocation_id')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->removeItemFromLocation($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError();
        }
    }

    /**
     * Mueve el item de ubicacion utilizando scanner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function moveItemScan(Request $request)
    {
        if (!$request->has('warehouse_id_to') || !$request->has('warehouse_id_from') || !$request->has('mapped_string_to') || !$request->has('mapped_string_from') || !$request->has('sku')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->moveItem($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError();
        }
    }

    /**
     * Mueve el item de ubicacion utilizando la web.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function moveItemWeb(Request $request)
    {
        if (!$request->has('warehouse_id_to') || !$request->has('warehouse_id_from') || !$request->has('mapped_string_to') || !$request->has('mapped_string_from') || !$request->has('sku')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->moveItem($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError();
        }
    }

    /**
     * Obtiene todas las ubicaciones actuales de los productos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getall(Request $request)
    {
        try {
            return $this->locationVariationRepository->getall($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }

    /**
     * Obtiene todas las variaciones de un producto en la ubicacion dada
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getItemsInLocation(Request $request)
    {
        if (!$request->has('warehouse_id') || !$request->has('mapped_string')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->getItemsInLocation($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }

    /**
     * Obtiene los productos mas recientemente ubicados de la bodega
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLatest(Request $request)
    {
        if (!$request->has('warehouse_id')) {
            return ApiResponses::badRequest('Parametros no validos.');
        }

        try {
            return $this->locationVariationRepository->getLatest($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }

    /**
     * Obtiene un resumen de la aplicacion
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSummary(Request $request)
    {
        try {
            return $this->locationVariationRepository->getSummary($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }

    /**
     * Imprime etiqueta de producto
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printSticker(Request $request)
    {
        $products = [];
        $pdf = app()->make('dompdf.wrapper');
        $paper_size = array(0,0,359.21,650);
        $pdf->setPaper($paper_size);
        $format = '';
        if ($request->get('format') === "landscape") {
            $paper_size = array(0,0,649.13,400);
            $pdf->setPaper($paper_size);
        }
        if ($request->has('product_id')) {
            if (is_numeric($request->product_id)) {
                $products[0] = \App\Product::find($request->product_id);
            } else {
                return ApiResponses::badRequest('product_id debe tener un valor numérico');
            }
        } elseif ($request->has('warehouselocation_id')) {
            if (is_numeric($request->warehouselocation_id)) {
                $products = \App\Product::whereHas('locations', function ($q) use ($request) {
                    $q->where('warehouselocation_id', $request->warehouselocation_id);
                })->get();
            }
        }
        foreach ($products as $key => $product) {
            $variations = $product->variations;
            if (sizeof($product->firstimg) > 0) {
                $image = '<img src="https://dsnegsjxz63ti.cloudfront.net/images/pg/g_'.$product->firstimg[0]->file.'" alt="" height="150px"/>';
            } else {
                $image = '';
            }
            $tableDescription = '<table>
                            <tr>
                                <td>Proveedor</td>
                                <td>'.$product->provider.'</td>
                            </tr>
                            <tr>
                                <td>Id ecom</td>
                                <td>'.$product->id.'</td>
                            </tr>
                            <tr>
                                <td>Semana</td>
                                <td>'.$product->created_at->format("W").' - '.$product->created_at->format("Y").'</td>
                            </tr>                        
                            <tr>
                                <td>Depto</td>
                                <td>'.$product->parent_name.'</td>
                            </tr>
                            <tr>
                                <td>Categoria</td>
                                <td>'.strtolower($product->family).'</td>
                            </tr>                        
                            <tr>
                                <td>Precio</td>
                                <td>$'.$variations[0]->price.'</td>
                            </tr>
                            <tr>
                                <td>Estatus</td>
                                <td>'.$this->tags[$product->price_status].'</td>
                            </tr>
                        </table>';
            $drawSKUS = '';
            
            foreach ($variations as $key => $v) {
                if ($v->stock > 0) {
                    $drawSKUS .= '<tr>
                                    <td align="center" style="font-size: 30px">'.$v->sku.'</td>
                                    <td align="center">'.$v->name.'</td>
                                    <td align="center">'.$v->stock.'</td>
                                    <td align="center">'.$v->color->name.'</td>
                                </tr>';
                }
            }
            $tableSKUS = '<table>
                            <tr>
                                <td>Actualizado</td>
                                <td colspan="3" align="right">'.date_format($variations[0]->updated_at, 'd/m/Y').'</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Nombre</td>
                                <td colspan="3" align="right">'.$product->name.'</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td align="center">SKU</td>
                                <td align="center">Talla</td>
                                <td align="center">Pzs</td>
                                <td align="center">Color</td>
                            </tr>                        
                            '.$drawSKUS.'
                        </table>';
            $barcode = '<div style="display:inline-block;text-align:center"><img src="data:image/png;base64,' . DNS1D::getBarcodePNG($product->internal_reference, "C128", 2, 70, array(5,5,5)) . '" alt="barcode"   /><br/>'.$product->internal_reference.'</div>';
            if ($request->get('format') === "landscape") {
                $format .= '<div style="font-family: sans-serif;page-break-after: always">
                    <div style="display:inline-block; font-size: 20px">
                    '.$image.'
                    </div>
                    <div style="display:inline-block;margin-left: 20px; font-size: 20px">
                        '.$tableDescription.'
                    </div>
                    <div style="display:inline-block;margin-left: 30px; font-size: 20px">
                        '.$tableSKUS.'
                    </div>
                    <div style="font-size: 20px">
                    '.$barcode.'
                    </div>                   
                </div>';
            } else {
                $format .= '<div style="font-family: sans-serif;page-break-after: always">
                    <div style="margin-bottom: 45px; font-size: 20px">
                    '.$barcode.'
                    </div>
                    <div style="display:inline-block; font-size: 20px">
                        '.$tableDescription.'
                    </div>
                    <div style="display:inline-block;margin-left: 30px; font-size: 20px">
                        '.$image.'
                    </div>
                    <div style="font-size: 20px">
                    '.$tableSKUS.'
                    </div>
                </div>';
            }
        }
        $head = '<head>
                <style>
                    table td {
                        padding: 0 6px 0 0;
                        align: center;

                    }                   
                </style>
            </head>';
        $body = $head.'<body>'.$format.'</body>';
        $pdf->loadHTML($format);
        return $pdf->stream();
    }

    public function importLocations()
    {
                
        $file_n = storage_path('app/locations_12_dic_2019.csv');
        $file = fopen($file_n, "r");
        $all_data = array();
        while (($data = fgetcsv($file, 1000, ",")) !== false) {
             $variation_id = $data[0];
             $locationString = $data[1];
             $locationStringArray = explode('-', $locationString);
            foreach ($locationStringArray as $key => $v) {
                $res = preg_replace("/[^0-9]/", "", $v);
                $res = intval($res);
                if ($key == 0) {
                    $letter = 'R';
                }
                if ($key == 1) {
                    $letter = 'B';
                }
                if ($key == 2) {
                    $letter = 'N';
                }

                if ($res < 10) {
                    $locationStringArray[$key] = $letter.'0'.$res;
                } else {
                    $locationStringArray[$key] = $letter.$res;
                }
            }
            $locationString = implode('-', $locationStringArray);
             $variation = Variation::find($variation_id);
             $warehouselocation = WarehouseLocation::where('mapped_string', $locationString)->first();
            if (!empty($warehouselocation) && !empty($variation)) {
                $lv = new LocationVariation;
                $lv->variation_id = $variation->id;
                $lv->warehouselocation_id = $warehouselocation->id;
                $lv->product_id = $variation->product_id;
                $lv->user_id = 1;
                $lv->save();
            }
        }

        return ApiResponses::ok();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
