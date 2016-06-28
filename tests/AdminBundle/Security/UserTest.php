<?php
namespace Tests\AdminBundle\Security;

use AdminBundle\Security\User;

class UserTest extends \PHPUnit_Framework_TestCase
{

    private $dataMock;

    protected function setUp()
    {
        $this->dataMock = (object) [
            "id" => 1,
            "username" => "test",
            "enmail" => "test@test.com",
            "enabled" => true
        ];
    }

    public function testConstructor()
    {
        $user = new User($this->dataMock, ['ROLE_ADMIN']);
        $this->assertInstanceOf('AdminBundle\Security\User', $user);
    }

    public function testSetToken()
    {
        $user = new User($this->dataMock, ['ROLE_ADMIN']);
        $user = $user->setToken('testtoken');
        $this->assertInstanceOf('AdminBundle\Security\User', $user);
    }

    public function testGetToken()
    {
        $user = new User($this->dataMock, ['ROLE_ADMIN']);
        $user = $user->setToken('testtoken');
        $this->assertEquals('testtoken', $user->getToken());
    }

    public function testGetRoles()
    {
        $user = new User($this->dataMock, []);
        $this->assertEquals(array(), $user->getRoles());

        $user = new User($this->dataMock, ['ROLE_ADMIN']);
        $this->assertEquals(['ROLE_ADMIN'], $user->getRoles());
    }

    public function testGetPassword()
    {
        $user = new User($this->dataMock, []);
        $this->assertNull($user->getPassword());
    }

    public function testGetSalt()
    {
        $user = new User($this->dataMock, []);
        $this->assertNull($user->getSalt());
    }

    public function testGetUsername()
    {
        $user = new User($this->dataMock, []);
        $this->assertEquals($this->dataMock->username, $user->getUsername());
    }

    public function testIsEqual()
    {
        $user1 = new User($this->dataMock, []);
        $user2 = new User($this->dataMock, []);
        $this->assertTrue($user1->isEqualTo($user2));
    }

}