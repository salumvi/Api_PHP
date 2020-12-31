<?php

namespace App\Test\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class BookControllerTest extends WebTestCase
{
    public function testCreateBookInvalidDataa()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/book',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"title" : ""}'
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testCreateBookNoData()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/book',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }


    public function testCreateBookValidDataa()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/book',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"title" : "Titulo"}'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}