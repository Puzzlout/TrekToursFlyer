<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

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

    public function testAnalytics()
    {
        $client = static::createClient();
        $tracking =  $client->getKernel()->getContainer()->getParameter('google_analytics');
        if(is_null($tracking))
        {
            $tracking = '';
        }
        //with usr_cc terms set to 1
        $cookie = new Cookie('usr_cc', 1);
        $client->getCookieJar()->set($cookie);
        $crawler = $client->request('GET','/en/');
        $this->assertEquals(1, $crawler->filter('#analytics-script')->first()->count());
        $this->assertContains($tracking, $crawler->filter('#analytics-script')->first()->text());

        //without usr_cc terms set
        $client = static::createClient();
        $crawler = $client->request('GET','/en/');
        $this->assertEquals(0, $crawler->filter('#analytics-script')->first()->count());

        //with usr_cc terms set to 0
        $client = static::createClient();
        $cookie = new Cookie('usr_cc', 0);
        $client->getCookieJar()->set($cookie);
        $crawler = $client->request('GET','/en/');
        $this->assertEquals(0, $crawler->filter('#analytics-script')->first()->count());
    }
}
