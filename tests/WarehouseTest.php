<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class WarehouseTest extends TestCase
{
    /** @test */
    public function testGetWarehouses()
    {                
        $this->disableExceptionHandling();

        $header = [];
        $header['Accept'] = '';
        $header['Authorization'] = 'Bearer '.config('phpunit.test_token');

        $this->get('/api/v1/warehouses/getall', $header)->assertResponseStatus(200);
    }
}
