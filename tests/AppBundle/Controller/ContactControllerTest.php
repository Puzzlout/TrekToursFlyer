<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testShowContact()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en/contact');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('contact', $crawler->filter('h2')->text());
    }
}
