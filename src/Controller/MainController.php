<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/change-locale/{locale}", name="change_locale")
     */
    public function changeLocale($locale,Request $request)
    {
        //Stocke la langue demandé dans la $session = $request->getSession()
        $request->getSession()->set('_locale',$locale);

       // On revient sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }
}
