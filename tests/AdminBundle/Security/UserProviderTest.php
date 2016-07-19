<?php
namespace Tests\AdminBundle\Security;

use AdminBundle\Security\User;
use AdminBundle\Security\UserProvider;

class UserProviderTest extends \PHPUnit_Framework_TestCase
{
    private $apiService;
    private $dataMock;

    protected function setUp()
    {
        $this->apiService = $this
            ->getMockBuilder('AdminBundle\Security\ApiService')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataMock = (object) [
            "id" => 1,
            "username" => "test",
            "enmail" => "test@test.com",
            "enabled" => true
        ];

    }

    public function testConstructor()
    {
        $userProvider = new UserProvider($this->apiService);
        $this->assertInstanceOf('AdminBundle\Security\UserProvider', $userProvider);
    }

    public function testLoadUserByCredentials()
    {
        $jwt = (object) ["token" => "testtoken"];
        $profile = (object) [
            "id" => 1,
            "username" => "test",
            "email" => "test@test.com",
            "enabled" => true,
            "roles" => ["ROLE_ADMIN"]
        ];
        $this->apiService->expects($this->once())->method('postLogin')->willReturn($jwt);
        $this->apiService->expects($this->once())->method('getUserProfile')->willReturn($profile);
        $userProvider = new UserProvider($this->apiService);

        $user = $userProvider->loadUserByCredentials('test', 'test');
        $this->assertInstanceOf('AdminBundle\Security\User', $user);
    }

    public function testLoadUserByCredentialsFalse()
    {
        $emptyJwt = (object)[];
        $this->apiService->expects($this->once())->method('postLogin')->willReturn($emptyJwt);
        $userProvider = new UserProvider($this->apiService);
        $user = $userProvider->loadUserByCredentials('test', 'test');
        $this->assertFalse($user);
    }

    /**
     * @expectedException Symfony\Component\Intl\Exception\NotImplementedException
     */
    public function testLoadByUsername()
    {
        $userProvider = new UserProvider($this->apiService);
        $userProvider->loadUserByUsername('test');
    }

    public function testGetAnonymousUser()
    {
        $userProvider = new UserProvider($this->apiService);
        $anonymousUser = $userProvider->getAnonymousUser();
        $this->assertInstanceOf('AdminBundle\Security\User', $anonymousUser);
        $this->assertInstanceOf('AdminBundle\Security\AnonymousUser', $anonymousUser);
    }

    public function testRefreshUser()
    {
        $user = new User($this->dataMock, ["ROLE_ADMIN"]);
        $userProvider = new UserProvider($this->apiService);
        $user = $userProvider->refreshUser($user);
        $this->assertInstanceOf('AdminBundle\Security\User', $user);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testRefreshUserException()
    {
        $userProvider = new UserProvider($this->apiService);
        $mockUser = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')->getMock();
        $userProvider->refreshUser($mockUser);
    }

    public function testSupportsClass()
    {
        $userProvider = new UserProvider($this->apiService);
        $this->assertTrue($userProvider->supportsClass('AdminBundle\Security\User'));
        $mockUser = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')->getMock();
        $this->assertFalse($userProvider->supportsClass(get_class($mockUser)));
    }

}