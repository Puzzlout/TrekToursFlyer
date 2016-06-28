<?php
namespace Tests\AdminBundle\Security;

use AdminBundle\Security\AnonymousUser;
use AdminBundle\Security\JWTAuthenticator;
use AdminBundle\Security\User;
use AdminBundle\Security\UserProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class JWTAuthenticatorTest extends \PHPUnit_Framework_TestCase
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
        $JWTAuthenticator = new JWTAuthenticator($this->apiService);
        $this->assertInstanceOf('AdminBundle\Security\JWTAuthenticator', $JWTAuthenticator);
    }

    public function testCreateToken()
    {
        $request = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $JWTAuthenticator = new JWTAuthenticator($this->apiService);
        $usernamePasswordToken = $JWTAuthenticator->createToken($request, 'test', 'test', 'test');
        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken',
            $usernamePasswordToken);
    }

    public function testAuthenticateToken()
    {
        $userToken = new UsernamePasswordToken('test', 'test', 'test');

        $userProviderUser = new User($this->dataMock, ["ROLE_ADMIN"]);
        $userProvider = $this->getMockBuilder('AdminBundle\Security\UserProvider')->disableOriginalConstructor()
            ->getMock();
        $userProvider->expects($this->once())->method('loadUserByCredentials')->willReturn($userProviderUser);

        $JWTAuthenticator = new JWTAuthenticator($this->apiService);
        $usernamePasswordToken = $JWTAuthenticator->authenticateToken($userToken, $userProvider, 'test');
        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken',
            $usernamePasswordToken);
    }

    public function testAuthenticateTokenAnonymousUser()
    {
        $userProvider = $this->getMockBuilder('AdminBundle\Security\UserProvider')->disableOriginalConstructor()
            ->getMock();
        $userProvider->expects($this->once())->method('getAnonymousUser')->willReturn(new AnonymousUser());
        $JWTAuthenticator = new JWTAuthenticator($this->apiService);
        $userToken = new UsernamePasswordToken('test', 'test', 'test');
        $userToken->eraseCredentials();
        $usernamePasswordToken = $JWTAuthenticator->authenticateToken($userToken, $userProvider, 'test');
        $this->assertInstanceOf('AdminBundle\Security\AnonymousUser', $usernamePasswordToken->getUser());
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testAuthenticateTokenInvalidArgument()
    {
        $userProvider = $this->getMockBuilder('AdminBundle\Security\UserProvider')->disableOriginalConstructor()
            ->getMock();
        $userToken = new UsernamePasswordToken('test', false, 'test');
        $JWTAuthenticator = new JWTAuthenticator($this->apiService);
        $JWTAuthenticator->authenticateToken($userToken, $userProvider, 'test');
    }
}