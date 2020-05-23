<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Rack;
use App\Repositories\WarehouseLocationRepository;
use App\WarehouseLocation;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;
use Zebra\Client;
use Zebra\Zpl\Image;
use Zebra\Zpl\Builder;
use Zebra\Zpl\GdDecoder;

use Validator;

class WarehouseLocationController extends Controller
{
    /**
     * @var Resource
     */
    protected $warehouseLocationRepository;

    public function __construct(Request $request)
    {
        $this->warehouseLocationRepository = new WarehouseLocationRepository();
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
        //
    }

    public function updateLocations()
    {
        $warehouselocations = WarehouseLocation::get();
        foreach ($warehouselocations as $key => $wl) {
            $rack = 'R'.$wl->rack;
            $block = 'B'.$wl->block;
            $level = 'N'.$wl->level;
            if ($wl->rack < 10) {
                $rack = 'R0'.$wl->rack;
                $change = true;
            }
            if ($wl->block < 10) {
                $block = 'B0'.$wl->block;
                $change = true;
            }
            if ($wl->level < 10) {
                $level = 'N0'.$wl->level;
                $change = true;
            }
            if ($change === true) {
                $mapped_string = $rack.'-'.$block.'-'.$level;
                $wl->mapped_string = $mapped_string;
                $wl->save();
            }
        }
        return ApiResponses::ok();
    }

    /**
     * Obtener todas las ubicaciones de determinada Bodega.
     *
     * @return \Illuminate\Http\Response
     */
    public function editLocationActive(Request $request)
    {
        try {
            return $this->warehouseLocationRepository->editLocationActive($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    /**
     * Obtener todas las ubicaciones de determinada Bodega.
     *
     * @return \Illuminate\Http\Response
     */
    public function getall(Request $request)
    {
        try {
            return $this->warehouseLocationRepository->getlocations($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
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
        if ($request->has('rack_id')) {
            $rack = Rack::find($request->rack_id);
            $warehouseLocations = $rack->warehouselocations;
            $pdf = app()->make('dompdf.wrapper');
            $pdf->setPaper('c7', 'landscape');
            $format = '<body>';
            foreach ($warehouseLocations as $key => $wl) {
                $barcode = '<div style="display:inline-block;text-align:center;font-size:35px"><img src="data:image/png;base64,' . DNS1D::getBarcodePNG($wl->mapped_string, "C128", 2, 130, array(5,5,5)) . '" alt="barcode"   /><br/>'.$wl->mapped_string.'</div>';
                $format .= '<div style="font-family: sans-serif;margin-left: 15px">              
                    <br/>
                    <div>
                    '.$barcode.'
                    </div>                   
                </div>';
            }
            $format .= '</body>';
            $pdf->loadHTML($format);
            return $pdf->stream();
        } elseif ($request->has('warehouselocation_id')) {
            $warehouselocation = WarehouseLocation::find($request->warehouselocation_id);
            $pdf = app()->make('dompdf.wrapper');
            $pdf->setPaper('c7', 'landscape');
            $format = '<body>';
            $barcode = '<div style="display:inline-block;text-align:center;font-size:35px"><img src="data:image/png;base64,' . DNS1D::getBarcodePNG($warehouselocation->mapped_string, "C128", 2, 130, array(5,5,5)) . '" alt="barcode"   /><br/>'.$warehouselocation->mapped_string.'</div>';
            $format .= '<div style="font-family: sans-serif;margin-left: 15px">                                
                    <br/>
                    <div>
                    '.$barcode.'
                    </div>                   
                </div>';
            $format .= '</body>';
            $pdf->loadHTML($format);
            return $pdf->stream();
        }
        return ApiResponses::badRequest();
    }

    /**
     * Obtener todos los bloques relacionados a un rack y a una bodega.
     *
     * @return \Illuminate\Http\Response
     */
    public function getblocks(Request $request)
    {
        try {
            return $this->warehouseLocationRepository->getblocks($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }

    /**
     * Obtiene la lista de racks de determinada bodega.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getracks(Request $request)
    {
        try {
            return $this->warehouseLocationRepository->getracks($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError();
        }
    }

    /**
     * Mapea ubicaciones en una bodega (agregar un rack).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function maplocations(Request $request)
    {
        $v = Validator::make($request->all(), $this->warehouseLocationRepository->getRules());
        if ($v->fails()) {
            return responder()->error('field_validation_error', 'Los campos no pasaron la prueba de validacion 3. Verifique sus campos')->respond();
        }
        return $this->warehouseLocationRepository->mapLocations($request->warehouse_id, $request->blocks, $request->levels, $request->sides, $request->portage ?? null);
    }

    /**
     * Inserta bloques en un rack.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function insertBlocks(Request $request)
    {
        return $this->warehouseLocationRepository->insertBlocks($request->rack_id);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function zplTest()
    {
        $imageString = DNS1D::getBarcodePNG('R01-B01-N01', "C128", 2, 130, array(5,5,5));
        $imageString = base64_decode($imageString);
        $decoder = GdDecoder::fromString($imageString);
        $image = new Image($decoder);

        $zpl = new Builder();
        $zpl->fo(50, 50)->gf($image)->fs();

        $client = new Client('172.17.31.142');
        $client->send($zpl);
        return ApiResponses::ok('Intentando imprimir...');
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
