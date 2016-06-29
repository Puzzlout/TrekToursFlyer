<?php
namespace Tests\AdminBundle\Security;

use AdminBundle\Security\AnonymousUser;

class AnonymousUserTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $anonymousUser = new AnonymousUser();
        $this->assertInstanceOf('AdminBundle\Security\AnonymousUser', $anonymousUser);
        $this->assertInstanceOf('AdminBundle\Security\User', $anonymousUser);
    }

    public function testGetUsername()
    {
        $anonymousUser = new AnonymousUser();
        $this->assertNull($anonymousUser->getUsername());
    }

}