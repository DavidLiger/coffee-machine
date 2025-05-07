<?php

// tests/CoffeeOrderControllerTest.php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoffeeOrderControllerTest extends WebTestCase
{
    public function testOrderCoffee()
    {
        $client = static::createClient();
        $client->request('POST', '/order', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'type' => 'Espresso',
            'intensity' => 'strong',
            'size' => 'small'
        ]));

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['status' => 'Order received']);
    }

    public function testStartProcess()
    {
        $client = static::createClient();
        $client->request('POST', '/start');

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['status' => 'Process started']);
    }
}
