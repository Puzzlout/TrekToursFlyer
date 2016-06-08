<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommonModuleControllerTest extends WebTestCase
{
    public function testHeaderModuleWithNoLang()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        //$this->assertContains('Home', $crawler->filter('ul.nav:first-child a')->text());
    }
    public function testHeaderModuleWithLang()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en');

        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        //$this->assertContains('Home', $crawler->filter('ul.nav:first-child a')->text());
    }
}
