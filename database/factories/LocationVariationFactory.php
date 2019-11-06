<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\LocationVariation;
use App\WarehouseLocation;
use App\Variation;
use App\Product;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(LocationVariation::class, function () {	
	$lv = [ 
			'warehouselocation_id' => 1, 
	        'variation_id'         => 1,
	        'product_id'		   => 1
	      ];
    return $lv;
});
