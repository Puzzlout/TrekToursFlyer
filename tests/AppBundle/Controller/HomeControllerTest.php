<?php
/**
 * Created by PhpStorm.
 * User: jl
 * Date: 03/06/16
 * Time: 06:29
 */

namespace tests\AppBundle\Controller;


class HomeControllerTest
{
    public function testShowContact()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Contact', $crawler->filter('h2')->text());
    }
}