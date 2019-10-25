<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\LocationVariation;
use App\WarehouseLocation;
use App\Variation;
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

$factory->define(LocationVariation::class, function (Faker $faker) {
	$variation = Variation::all()->random();
    return [        
    	'warehouselocation_id'       => WarehouseLocation::all()->random()->id, 
        'variation_id'      => $variation->id,
        'product_id'		=> $variation->product_id,
    ];
});
