<?php

namespace AdminBundle\Security;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserProvider implements UserProviderInterface
{
    protected $apiService;

    public function __construct(ApiService $apiService) {
        $this->apiService = $apiService;
    }

    public function loadUserByCredentials($username, $password) {
        $jwt = $this->apiService->postLogin($username, $password);
        if(isset($jwt->token)) {
            $data = $this->apiService->getUserProfile($jwt->token);
            $user = new User($data, $data->roles);
            $user->setToken($jwt->token);
            return $user;
        }
        return false;

    }

    public function loadUserByUsername($username)
    {
        throw new NotImplementedException('method not implemented');
    }

    public function getAnonymousUser()
    {
        return new AnonymousUser();
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        return $user;
    }
    public function supportsClass($class)
    {
        return $class === 'AdminBundle\Security\User';
    }
}