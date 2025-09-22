<?php

namespace App\Tests\Controller\Product;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ListControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/product?priceLessThan=100000');

        self::assertResponseIsSuccessful();
    }
}
