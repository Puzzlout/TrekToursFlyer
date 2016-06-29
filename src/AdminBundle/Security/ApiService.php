<?php
namespace AdminBundle\Security;
use Symfony\Component\Security\Core\User\User;
use Circle\RestClientBundle\Services\RestClient;

/**
 * Service that provides access to the API calls and JWT validation
 */
class ApiService {

    private $domain;
    private $restClient;

    /**
     * $param RestClient $restClient
     * @param string $domain
     */
    public function __construct(RestClient $restClient, $domain)
    {
        $this->restClient = $restClient;
        $this->domain = $domain;
    }
    /**
     * Get the User Profile based on the JWT (and validate it).
     *
     * @return string User info
     */
    public function getUserProfile($jwt)
    {
        $response = $this->restClient->get($this->domain.'/me', [
            CURLOPT_HTTPHEADER => ['Authorization: Bearer '.$jwt]
        ]);
        $profile = json_decode($response->getContent());
        return $profile;
    }

    public function postLogin($username, $password)
    {
        $response =  $this->restClient->post($this->domain.'/login',
            json_encode(['_username' => $username, '_password' => $password]));
        return json_decode($response->getContent());
    }
}