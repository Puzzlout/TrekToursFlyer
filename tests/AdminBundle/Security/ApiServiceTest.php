<?php
namespace Tests\AdminBundle\Security;

use AdminBundle\Security\ApiService;

class ApiServiceTest extends \PHPUnit_Framework_TestCase
{
    private $restClient;
    private $restResponse;
    private $testToken = "testtest";

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
    }

    public function testPostLogin()
    {
        $this->restResponse
            ->expects($this->once())
            ->method('getContent')
            ->willReturn('{"token": "'.$this->testToken.'"}');
        $this->restClient
            ->expects($this->once())
            ->method('post')
            ->willReturn($this->restResponse);

        $apiService = new ApiService($this->restClient, 'http://api.domain.com');
        $loginToken = $apiService->postLogin('test', 'test');
        $this->assertEquals($loginToken->token, $this->testToken);
    }

    public function testGetUserProfile()
    {
        $this->restResponse
            ->expects($this->once())
            ->method('getContent')
            ->willReturn('{"id":1,"username":"test","email":"test@test.com","enabled":true,"roles":["ROLE_ADMIN"]}');

        $this->restClient
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->restResponse);

        $apiService = new ApiService($this->restClient, 'http://api.domain.com');
        $profile = $apiService->getUserProfile($this->testToken);
        $this->assertEquals($profile->username, 'test');
        $this->assertEquals($profile->email, 'test@test.com');
        $this->assertTrue($profile->enabled);
        $this->assertEquals($profile->roles, ["ROLE_ADMIN"]);
    }

}