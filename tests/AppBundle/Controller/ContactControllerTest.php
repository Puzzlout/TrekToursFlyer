<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    private $restClient;
    private $restResponse;
    private $mockPostCRIResponse;

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
        $this->mockPostCRIResponse = json_encode(
            [
                "id" => 1,
                "email" => "test@test.com",
                "first_name" => "Test",
                "last_name" => "Test",
                "phone_number" => "+111222333444",
                "has_sent_copy_to_client" => true,
                "message" => "This is test message",
                "status" => "TBP",
                "created" => "2016-05-23T18:51:55+0200",
                "updated" => "2016-05-23T18:51:55+0200"
            ]
        );
    }

    public function testShowContact()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/contact');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div#customer_info_request')->count());
        $this->assertContains('contact', $crawler->filter('h2')->text());

        //fetch form to and setup fields

        $formButton = $crawler->selectButton('customer_info_request_send');
        $form = $formButton->form([
            'customer_info_request[first_name]' => 'Test',
            'customer_info_request[last_name]' => 'Test',
            'customer_info_request[email]' => 'test@test.com',
            'customer_info_request[phone_number]' => '+111222333444',
            'customer_info_request[has_sent_copy_to_client]' => 1,
            'customer_info_request[message]' => 'This is test message',
            'customer_info_request[send]' => ''
        ], 'POST');
        $client = static::createClient();
        $this->restResponse->expects($this->once())->method('getStatusCode')->willReturn(201);
        $this->restClient->expects($this->once())->method('post')->willReturn($this->restResponse);
        $client->getContainer()->set('circle.restclient', $this->restClient);
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
        $this->assertEquals(0, $crawler->filter('div.alert-danger')->count());
    }

    public function testShowContactApiError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/contact');

        $formButton = $crawler->selectButton('customer_info_request_send');
        $form = $formButton->form([
            'customer_info_request[first_name]' => 'Test',
            'customer_info_request[last_name]' => 'Test',
            'customer_info_request[email]' => 'test@test.com',
            'customer_info_request[phone_number]' => '+111222333444',
            'customer_info_request[message]' => 'This is test message'
        ]);
        $client = static::createClient();
        $this->restResponse->expects($this->once())->method('getStatusCode')->willReturn(403);
        $this->restClient->expects($this->once())->method('post')->willReturn($this->restResponse);

        $client->getContainer()->set('circle.restclient', $this->restClient);
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-danger')->count());
        $this->assertEquals(0, $crawler->filter('div.alert-success')->count());
    }
}
