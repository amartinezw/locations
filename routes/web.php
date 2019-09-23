<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/routes', function () use ($router) {
	dump(app('router')->getRoutes());
});

$router->group([
    'middleware' => 'client',
    'prefix'	 => 'api/v1',
], function (\Laravel\Lumen\Routing\Router $router) {
    $router->get('/test-client', function () {
        return 'autenticado.';
    });

    $router->get('/warehouses/getall', 'WarehouseController@getall');
    $router->get('warehouselocations/getracks', 'WarehouseLocationController@getracks');
    $router->get('warehouselocations/getblocks', 'WarehouseLocationController@getblocks');
    $router->get('warehouselocations/getall', 'WarehouseLocationController@getall');
    $router->get('locationvariation/getall', 'LocationVariationController@getall');
    $router->get('locationvariation/getitemsinlocation', 'LocationVariationController@getItemsInLocation');

    $router->post('/warehouses/store', 'WarehouseController@store');
    $router->post('/warehouses/edit', 'WarehouseController@edit');
    $router->post('warehouselocations/maplocations', 'WarehouseLocationController@maplocations');
    $router->post('locationvariation/locateitemscan', 'LocationVariationController@locateItemScan');
    $router->post('locationvariation/moveitemscan', 'LocationVariationController@moveItemScan');
    $router->post('locationvariation/locateitemweb', 'LocationVariationController@locateItemWeb');
    $router->post('locationvariation/moveitemweb', 'LocationVariationController@moveItemWeb');
});
