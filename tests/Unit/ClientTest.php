<?php

namespace Tests\Unit;

use App\Models\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /**
     *
     * @return void
     */
    public function test_check_if_all_clients_fillAble_properties_are_correctly_created()
    {
        $clientFillAblePropertiesExpected = [
            "company_name",
            "VAT",
            "address",
        ];

        $client = new Client();

        $clientPropertiesIntersect = array_intersect($clientFillAblePropertiesExpected, $client->getFillable());

        $this->assertEquals($clientPropertiesIntersect, $clientFillAblePropertiesExpected);
        $this->assertEquals(sizeof($clientFillAblePropertiesExpected), sizeof($client->getFillable()));
    }
}
