<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    private $restClient;
    private $restResponse;
    private $captchaResponse;
    private $mockPostCRIResponse;
    private $mockCaptchaResponse;
    private $mailer;

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
        $this->captchaResponse = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mailer = $this->getMockBuilder('Swift_Mailer')->disableOriginalConstructor()->getMock();
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
        $this->mockCaptchaResponse['failed'] = json_encode([ "success" => false ]);
        $this->mockCaptchaResponse['successful'] = json_encode([ "success" => true ]);
    }

    public function testShowContact()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/contact');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div#customer_info_request')->count());
        $this->assertContains('contact', strtolower($crawler->filter('#customer_info_request > div.row.contact-form-title > div > h1')->text()));

        //fetch form to and setup fields

        $formButton = $crawler->selectButton('customer_info_request_send');
        $form = $formButton->form([
            'customer_info_request[first_name]' => 'Test',
            'customer_info_request[last_name]' => 'Test',
            'customer_info_request[email]' => 'test@test.com',
            'customer_info_request[phone_number]' => '+111222333444',
            'customer_info_request[send_copy_to_client]' => 1,
            'customer_info_request[message]' => 'This is test message',
            'customer_info_request[send]' => ''
        ], 'POST');
        $client = static::createClient();
        $this->restResponse->expects($this->once())->method('getStatusCode')->willReturn(201);
        $this->restResponse->expects($this->once())->method('getContent')->willReturn($this->mockPostCRIResponse);
        $this->captchaResponse->expects($this->once())->method('getContent')
            ->willReturn($this->mockCaptchaResponse['successful']);
        $this->restClient->expects($this->at(0))->method('post')->willReturn($this->captchaResponse);
        $this->restClient->expects($this->at(1))->method('post')->willReturn($this->restResponse);
        $this->restClient->expects($this->once())->method('patch')->willReturn($this->restResponse);
        $this->mailer->expects($this->any())->method('send')->willReturn(1);
        $client->getContainer()->set('mailer', $this->mailer);
        $client->getContainer()->set('circle.restclient', $this->restClient);
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
        $this->assertEquals(0, $crawler->filter('div.alert-danger')->count());
    }


    /**
     * @expectsException \Exception
     */
    public function testShowContactMailerExceptionAdmin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/contact');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div#customer_info_request')->count());
        $this->assertContains('contact', strtolower($crawler->filter('#customer_info_request > div.row.contact-form-title > div > h1')->text()));

        //fetch form to and setup fields

        $formButton = $crawler->selectButton('customer_info_request_send');
        $form = $formButton->form([
            'customer_info_request[first_name]' => 'Test',
            'customer_info_request[last_name]' => 'Test',
            'customer_info_request[email]' => 'test@test.com',
            'customer_info_request[phone_number]' => '+111222333444',
            'customer_info_request[send_copy_to_client]' => 1,
            'customer_info_request[message]' => 'This is test message',
            'customer_info_request[send]' => ''
        ], 'POST');

        $client = static::createClient();
        $this->restResponse->expects($this->once())->method('getStatusCode')->willReturn(201);
        $this->restResponse->expects($this->once())->method('getContent')->willReturn($this->mockPostCRIResponse);
        $this->captchaResponse->expects($this->once())->method('getContent')
            ->willReturn($this->mockCaptchaResponse['successful']);
        $this->restClient->expects($this->at(0))->method('post')->willReturn($this->captchaResponse);
        $this->restClient->expects($this->at(1))->method('post')->willReturn($this->restResponse);
        $this->mailer->expects($this->any())->method('send')->willThrowException(new \Exception('test'));
        $client->getContainer()->set('mailer', $this->mailer);
        $client->getContainer()->set('circle.restclient', $this->restClient);
        $client->submit($form);
    }

    /**
     * @expectsException \Exception
     */
    public function testShowContactMailerExceptionClient()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/contact');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div#customer_info_request')->count());
        $this->assertContains('contact', strtolower($crawler->filter('#customer_info_request > div.row.contact-form-title > div > h1')->text()));

        //fetch form to and setup fields

        $formButton = $crawler->selectButton('customer_info_request_send');
        $form = $formButton->form([
            'customer_info_request[first_name]' => 'Test',
            'customer_info_request[last_name]' => 'Test',
            'customer_info_request[email]' => 'test@test.com',
            'customer_info_request[phone_number]' => '+111222333444',
            'customer_info_request[send_copy_to_client]' => 1,
            'customer_info_request[message]' => 'This is test message',
            'customer_info_request[send]' => ''
        ], 'POST');

        $client = static::createClient();
        $this->restResponse->expects($this->once())->method('getStatusCode')->willReturn(201);
        $this->restResponse->expects($this->once())->method('getContent')->willReturn($this->mockPostCRIResponse);
        $this->captchaResponse->expects($this->once())->method('getContent')
            ->willReturn($this->mockCaptchaResponse['successful']);
        $this->restClient->expects($this->at(0))->method('post')->willReturn($this->captchaResponse);
        $this->restClient->expects($this->at(1))->method('post')->willReturn($this->restResponse);
        $this->mailer->expects($this->at(0))->method('send')->willReturn(1);
        $this->mailer->expects($this->at(1))->method('send')->willThrowException(new \Exception('test'));
        $client->getContainer()->set('mailer', $this->mailer);
        $client->getContainer()->set('circle.restclient', $this->restClient);
        $client->submit($form);
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
        $this->captchaResponse->expects($this->once())->method('getContent')
            ->willReturn($this->mockCaptchaResponse['successful']);
        $this->restClient->expects($this->at(0))->method('post')->willReturn($this->captchaResponse);
        $this->restClient->expects($this->at(1))->method('post')->willReturn($this->restResponse);

        $client->getContainer()->set('circle.restclient', $this->restClient);
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-danger')->count());
        $this->assertEquals(0, $crawler->filter('div.alert-success')->count());
    }

    public function testShowContactReCaptchaError()
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
        $this->captchaResponse->expects($this->once())->method('getContent')
            ->willReturn($this->mockCaptchaResponse['failed']);
        $this->restClient->expects($this->at(0))->method('post')->willReturn($this->captchaResponse);

        $client->getContainer()->set('circle.restclient', $this->restClient);
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-danger')->count());
        $this->assertEquals(0, $crawler->filter('div.alert-success')->count());
    }
}
