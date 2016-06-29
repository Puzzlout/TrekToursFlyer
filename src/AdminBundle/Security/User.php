<?php

namespace AdminBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class User implements UserInterface, EquatableInterface
{
    private $data;
    private $roles;
    private $token;

    public function __construct($data, array $roles)
    {
        $this->data = $data;
        $this->roles = $roles;
    }

    public function getToken()
    {
        return $this->token ?? '';
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->data->username;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}