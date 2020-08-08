<?php

namespace App\Security;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;

class MyEntityUserProvider extends EntityUserProvider{

  public function loadUserByOAuthUserResponse(UserResponseInterface $response)
  {
    
  }
}