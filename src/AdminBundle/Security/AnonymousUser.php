<?php
namespace AdminBundle\Security;

class AnonymousUser extends User {

    public function __construct()
    {
        parent::__construct(null,array('IS_AUTHENTICATED_ANONYMOUSLY'));
    }

    public function getUsername()
    {
        return null;
    }
}