<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PingControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ping');

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals("ok", $response['status']);
        $this->assertEquals("pong", $response['message']);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('pong', $client->getResponse()->getContent());
        $this->assertResponseIsSuccessful();
    }
}
