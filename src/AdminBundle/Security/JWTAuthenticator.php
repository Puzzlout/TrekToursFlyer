<?php
namespace AdminBundle\Security;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

class JWTAuthenticator implements SimpleFormAuthenticatorInterface
{
    use ContainerAwareTrait;

    protected $apiIService;

    public function __construct(apiService $apiIService)
    {
        $this->apiIService = $apiIService;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    /**
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param $providerKey
     *
     * @return UsernamePasswordToken
     *
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        // The user provider should implement UserProviderInterface
        /*if (!$userProvider instanceof UserProviderInterface) {
            throw new InvalidArgumentException('Argument must implement UserProviderInterface');
        }*/
        if ($token->getCredentials() === null) {
            $user = $userProvider->getAnonymousUser();
        } else {
            // Get the user for the injected UserProvider
            try {
                $user = $userProvider->loadUserByCredentials($token->getUsername(), $token->getCredentials());
            } catch (\Exception $e) {
                throw new AuthenticationException(sprintf('API error.'));
            }
            if (!$user) {
                throw new AuthenticationException(sprintf('Invalid Credentials.'));
            }
        }
        return new UsernamePasswordToken(
            $user,
            $token,
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken;
    }

}