<?php

use Illuminate\Database\Seeder;

class LocationVariationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$RandomProducts = App\Product::inRandomOrder()->limit(100)->get();
    	$RandomLocations = App\WarehouseLocation::inRandomOrder()->limit(100)->get();    	
    	foreach($RandomProducts as $k => $prod) {
    		foreach ($prod->variations as $key => $var) {
		        factory(App\LocationVariation::class)->create([
		        	'warehouselocation_id' => $RandomLocations[$k]->id,
		        	'variation_id' => $var->id,
		        	'product_id' => $prod->id
		        ]);
    			
    		}
    	}
    }
}
