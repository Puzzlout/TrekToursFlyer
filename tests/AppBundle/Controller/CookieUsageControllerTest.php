<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CookieUsageControllerTest extends WebTestCase
{
    public function testShowCookieUsageAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $client->getContainer()->get('router')->generate('cookie_usage'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
