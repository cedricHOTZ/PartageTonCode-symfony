<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;


class UserChecker implements UserCheckerInterface
{

  public function checkPreAuth(UserInterface $user)
  {

    if (!$user instanceof AppUser) {


      return;
    }
    //  ajout de vérification sur le token d'activation 
    if (!is_null($user->getActivationToken())) {
      $ex = new LockedException("member not active");
      throw $ex;
    }
  }


  public function checkPostAuth(UserInterface $user)
  {
    if (!$user instanceof AppUser) {
      return;
    }
  }
}
