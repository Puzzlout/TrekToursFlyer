<?php
/**
 * Created by PhpStorm.
 * User: jl
 * Date: 03/06/16
 * Time: 06:29
 */

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testShowHome()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('/en/', $client->getResponse()->headers->get('location'));
        $crawler = $client->request('GET', $client->getResponse()->headers->get('location'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertContains('Welcome to Homepage', $crawler->filter('h1')->text());

        $client->request('GET', '/fr/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('fr', $client->getRequest()->attributes->get('_locale'));

        $client->request('GET', '/de/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('de', $client->getRequest()->attributes->get('_locale'));
    }
}
