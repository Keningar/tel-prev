<?php

namespace TelconetSSO\TelconetSSOBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class UserSSOToken extends AbstractToken
{
    public function __construct($uid = '', array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($uid);

        if (!empty($uid)) {
            $this->setAuthenticated(true);
        }
    }

    public function getCredentials()
    {
        return '';
    }
}