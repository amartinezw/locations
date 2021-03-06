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
    return 'Locations API.';
});

$router->get('/documentacion', function () use ($router) {
    return view('locations');
});

$router->get('/routes', function () use ($router) {
    dump(app('router')->getRoutes());
});

$router->get('warehouselocations/zpltest', 'WarehouseLocationController@zplTest');

$router->get('/check-token/{token}', 'UserApiController@checkToken');

$router->group([
    'middleware' => 'auth:api',
    'prefix'     => 'api/v1',
], function (\Laravel\Lumen\Routing\Router $router) {
    $router->get('/test-client', function () {
        return 'autenticado.';
    });
    $router->get('/warehouses/getall', 'WarehouseController@getall');
    $router->get('warehouselocations/getracks', 'WarehouseLocationController@getracks');
    $router->get('warehouselocations/getblocks', 'WarehouseLocationController@getblocks');
    $router->get('warehouselocations/updatelocations', 'WarehouseLocationController@updateLocations');
    $router->get('warehouselocations/updateportagelocations', 'WarehouseLocationController@updatePortageLocations');
    $router->get('warehouselocations/getall', 'WarehouseLocationController@getall');
    $router->get('warehouselocations/printsticker', 'WarehouseLocationController@printSticker');
    $router->get('locationvariation/getsummary', 'LocationVariationController@getSummary');
    $router->get('locationvariation/getall', 'LocationVariationController@getall');
    $router->get('locationvariation/importlocations', 'LocationVariationController@importLocations');
    $router->get('locationvariation/getlatest', 'LocationVariationController@getlatest');
    $router->get('locationvariation/getlocationsofitem', 'LocationVariationController@getLocationsOfItem');
    $router->get('locationvariation/getlocationsofproduct', 'LocationVariationController@getLocationsOfProduct');
    $router->get('locationvariation/getitemsinlocation', 'LocationVariationController@getItemsInLocation');
    $router->get('user/getusers', 'UserApiController@index');
    $router->get('user/getprofile', 'UserApiController@getProfile');
    $router->get('user/delete/{id}', 'UserApiController@delete');
    $router->get('roles/getall', 'RoleApiController@index');
    $router->get('roles/delete/{id}', 'RoleApiController@delete');
    $router->get('locationvariation/printsticker', 'LocationVariationController@printSticker');
    $router->get('stores/getallstores', 'StoreController@getAllForSelect');
    $router->get('categories/parent', 'CategoryApiController@getParentCategories');
    $router->get('categories/childs', 'CategoryApiController@getChildCategories');
    $router->get('categories/child/{parent_id}', 'CategoryApiController@getCategoriesOfParent');

    $router->post('/warehouses/store', 'WarehouseController@store');
    $router->post('/warehouses/update', 'WarehouseController@update');
    $router->post('/warehouses/destroy', 'WarehouseController@destroy');
    $router->post('warehouselocations/editlocationactive', 'WarehouseLocationController@editLocationActive');
    $router->post('warehouselocations/maplocations', 'WarehouseLocationController@maplocations');
    $router->post('warehouselocations/insertblocks', 'WarehouseLocationController@insertBlocks');
    $router->post('locationvariation/locateitemscan', 'LocationVariationController@locateItemScan');
    $router->post('locationvariation/moveitemscan', 'LocationVariationController@moveItemScan');
    $router->post('locationvariation/locateitemweb', 'LocationVariationController@locateItemWeb');
    $router->post('locationvariation/moveitemweb', 'LocationVariationController@moveItemWeb');
    $router->post('locationvariation/removeitemfromlocation', 'LocationVariationController@removeItemFromLocation');
    $router->post('user/authenticate', 'UserApiController@login');
    $router->post('user/create', 'UserApiController@store');
    $router->post('user/update/{id}', 'UserApiController@update');
    $router->post('roles/create', 'RoleApiController@store');
    $router->post('roles/update/{id}', 'RoleApiController@update');
});
