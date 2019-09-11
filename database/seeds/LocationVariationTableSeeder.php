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
        factory(App\LocationVariation::class, 100)->create();
    }
}
