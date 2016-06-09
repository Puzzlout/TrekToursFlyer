<?php

namespace Tests\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/admin/');
        $this->assertEquals(302, $client->getResponse()->getStatusCode(),'Assert that status code returned is 302');
        $this->assertContains('admin/login', $client->getResponse()->headers->get('location'),
            'Assert that unauthorized access redirects to admin/login');

        $crawler = $client->request('GET', '/admin/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Assert that status code returned is 200');
        $form = $crawler->selectButton('Login')->form();
        $form["_username"] = "admin";
        $form["_password"] = "test";
        $client->followRedirects(true);
        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('div#login-error')->count(),
            'Assert that failed login results in showing an error');

        //we login the client through the code so we don't show password in plaintext
        $client = $this->logIn($client);
        $crawler = $client->request('GET', '/admin/');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Welcome to Admin panel")')->count(),
            "GET /admin/ with authorization; Failed asserting that 'Welcome to Admin panel' is shown on page");
    }

    public function testLogout()
    {
        $client = static::createClient();

        //we must first login
        $client = $this->logIn($client);
        $crawler = $client->request('GET', '/admin/');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Welcome to Admin panel")')->count(),
            "GET /admin/ with authorization; Failed asserting that 'Welcome to Admin panel' is shown on page");

        $client->request('GET', '/admin/logout');
        $this->assertEquals('302', $client->getResponse()->getStatusCode(), 'Assert that logout redirects you');

        $client->request('GET', '/admin/');
        $this->assertEquals(302, $client->getResponse()->getStatusCode(),'Assert that status code returned is 302');
        $this->assertContains('admin/login', $client->getResponse()->headers->get('location'),
            'Assert that unauthorized access redirects to admin/login');
    }


    private function logIn($client)
    {
        $session = $client->getContainer()->get('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken('admin', null, $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
        return $client;
    }
}
