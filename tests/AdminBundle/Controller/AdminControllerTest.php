<?php

namespace Tests\AdminBundle\Controller;

use AdminBundle\Security\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminControllerTest extends WebTestCase
{
    private $restClient;
    private $mockGetCRIResponse;
    private $restResponse;

    protected function setUp()
    {
        $this->restClient = $this
            ->getMockBuilder('Circle\RestClientBundle\Services\RestClient')
            ->disableOriginalConstructor()
            ->getMock();
        $this->restResponse = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockGetCRIResponse = json_encode(
            [
                [
                    "id" => 1,
                    "email" => "test@test.com",
                    "first_name" => "test",
                    "last_name" => "test",
                    "status" => "TBP",
                    "created" => "2016-05-23T18:51:55+0200",
                    "updated" => "2016-05-23T18:51:55+0200"
                ],
                [
                    "id" => 2,
                    "email" => "test2@test.com",
                    "first_name" => "test2",
                    "last_name" => "test2",
                    "status" => "RTC",
                    "created" => "2016-05-27T18:00:00+0200",
                    "updated" => "2016-05-27T18:00:00+0200"
                ]
            ]
        );
    }

    public function testIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/admin/');
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'Assert that status code returned is 302');
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
            'Assert that failed login results is showing an error');

        //we login the client through the code so we don't show password in plaintext
        $client = $this->logIn($client);
        $crawler = $client->request("GET", "/admin/");
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
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'Assert that status code returned is 302');
        $this->assertContains('admin/login', $client->getResponse()->headers->get("location"),
            'Assert that unauthorized access redirects to admin/login');
    }

    public function testCustomerInfoRequestsAction()
    {
        $client = static::createClient();

        $client = $this->logIn($client);
        $this->restResponse->expects($this->once())->method('getContent')->willReturn($this->mockGetCRIResponse);
        $this->restResponse->expects($this->once())->method('getStatusCode')->willReturn(200);
        $this->restClient->expects($this->once())->method('get')->willReturn($this->restResponse);

        $client->getContainer()->set('circle.restclient', $this->restClient);
        $crawler = $client->request('GET', $client->getContainer()->get('router')->generate('admin_cri'),
            ['limit' => 2, 'from' => '2016-05-03', 'to' => '2016-06-03']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check filter form
        $this->assertEquals(1, $crawler->filter('input#from')->count());
        $this->assertEquals(1, $crawler->filter('input#to')->count());
        $this->assertEquals(1, $crawler->filter('select#limit')->count());

        //check if there are 3 rows in the table (2 for results and 1 for table header)
        $this->assertEquals(3, $crawler->filter('table.table-striped tr')->count());
    }

    /**
     * @expectsException \Exception
     */
    public function testCustomerInfoRequestsActionException()
    {
        $client = static::createClient();
        $client = $this->logIn($client);
        $this->restResponse->expects($this->once())->method('getStatusCode')->willReturn(403);
        $this->restClient->expects($this->once())->method('get')->willReturn($this->restResponse);
        $client->getContainer()->set('circle.restclient', $this->restClient);
        $crawler = $client->request('GET', $client->getContainer()->get('router')->generate('admin_cri'),
            ['limit' => 11, 'from' => '2016-07-03', 'to' => '2016-06-03']);
    }

    public function testUpdateStatusCustomerInfoRequests()
    {
        $client = static::createClient();
        $client = $this->logIn($client);
        $client->followRedirects(false);

        $this->restResponse->expects($this->once())->method('getStatusCode')->willReturn(200);
        $this->restClient->expects($this->once())->method('patch')->willReturn($this->restResponse);
        $client->getContainer()->set('circle.restclient', $this->restClient);
        $client->request('POST', $client->getContainer()->get('router')->generate('admin_cri_update_status', ["id" => 1]),
            [
                'status' => 'TBP'
            ],
            [],
            [
                'HTTP_Referer' => $client->getContainer()->get('router')->generate('admin_cri')
            ]
        );
        $this->assertNotEmpty($client->getContainer()->get('session')->getFlashBag()->get('admin_update_status'));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        //no status
        $client = static::createClient();
        $client = $this->logIn($client);
        $client->followRedirects(false);

        $client->getContainer()->set('circle.restclient', $this->restClient);
        $client->request('POST', $client->getContainer()->get('router')->generate('admin_cri_update_status', ["id" => 1]),
            [],
            [],
            [
                'HTTP_Referer' => $client->getContainer()->get('router')->generate('admin_cri')
            ]
        );
        $this->assertEmpty($client->getContainer()->get('session')->getFlashBag()->get('admin_update_status'));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }


    public function testUpdateStatusCustomerInfoRequestsException()
    {
        $client = static::createClient();
        $client = $this->logIn($client);
        $client->followRedirects(false);

        $this->restResponse->expects($this->once())->method('getStatusCode')->willReturn(403);
        $this->restClient->expects($this->once())->method('patch')->willReturn($this->restResponse);
        $client->getContainer()->set('circle.restclient', $this->restClient);
        $client->request('POST', $client->getContainer()->get('router')->generate('admin_cri_update_status', ["id" => 1]),
            [
                'status' => 'TBP'
            ],
            [],
            [
                'HTTP_Referer' => $client->getContainer()->get('router')->generate('admin_cri')
            ]
        );
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    private function logIn($client)
    {
        $userMockData = (object)[
            "id" => 1,
            "username" => "test",
            "enmail" => "test@test.com",
            "enabled" => true
        ];
        $session = $client->getContainer()->get('session');
        $userMock = new User($userMockData, ["ROLE_ADMIN"]);
        $firewall = 'main';
        $token = new UsernamePasswordToken($userMock, 'test', $firewall, $userMock->getRoles());
        $session->set("_security_" . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
        return $client;
    }
}
