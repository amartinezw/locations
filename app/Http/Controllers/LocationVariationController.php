<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiResponses;
use App\Repositories\LocationVariationRepository;
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
        if ($request->has('product_id')) {
            $pdf = app()->make('dompdf.wrapper');
            $product = \App\Product::find($request->product_id);

            $head = '<head>
                    <style>
                        table td {
                            padding: 0 6px 0 0;
                        }
                    </style>
                </head>';
            $image = '<img src="https://dsnegsjxz63ti.cloudfront.net/images/pg/g_'.$product->firstimg[0]->file.'" alt="" height="150px"/>';        
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
                                <td>$'.$product->user_price.'</td>
                            </tr>
                            <tr>
                                <td>Estatus</td>
                                <td>'.$this->tags[$product->price_status].'</td>
                            </tr>
                        </table>';
            $drawSKUS = '';
            $variations = $product->variations;
            foreach ($variations as $key => $v) {
                if ($v->stock > 0 && $v->active == 1) {
                    $drawSKUS .= '<tr>
                                    <td>'.$v->sku.'</td>
                                    <td>'.$v->name.'</td>
                                    <td>'.$v->stock.'</td>
                                    <td>'.$product->colors_es.'</td>
                                </tr>';    
                }
                
            }
            $tableSKUS = '<table>
                            <tr>
                                <td>Nombre</td>
                                <td colspan="2">'.$product->name.'</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>SKU</td>
                                <td>Talla</td>
                                <td>Pzs</td>
                                <td>Color</td>
                            </tr>                        
                            '.$drawSKUS.'
                        </table>';
            $barcode = '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($product->internal_reference, "C128",2,70,array(5,5,5), true) . '" alt="barcode"   />';
            if ($request->format === "landscape") {
                $pdf->setPaper('A4', 'landscape');   
                $format = $head.'
                <body>
                <div style="width: 650px; font-family: sans-serif">
                    <div style="display:inline-block">
                    '.$image.'
                    </div>
                    <div style="display:inline-block;margin-left: 20px; width: 150px">
                        '.$tableDescription.'
                    </div>
                    <div style="display:inline-block;margin-left: 30px; width: 300px">
                        '.$tableSKUS.'
                    </div>
                </div>
                '.$barcode.'
                </body>';
            } else {            
                $format = $head.'
                <body>
                <div style="width: 100%; font-family: sans-serif">
                    <div style="margin-bottom: 45px">
                    '.$barcode.'
                    </div>
                    <div style="display:inline-block; width: 150px">
                        '.$tableDescription.'
                    </div>
                    <div style="display:inline-block;margin-left: 30px; width: 300px">
                        '.$image.'
                    </div>
                    <div>
                    '.$tableSKUS.'
                    </div>
                </div>
                </body>';
            }                 
            $pdf->loadHTML($format);
            return $pdf->stream();
        } else {
            return ApiResponses::badRequest();
        }        
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
