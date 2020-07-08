<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{
    /**
     
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $em, PinRepository $pinRepository)
    {
     
    //Ajouter un pin manuellement

    //  $premier = new Pin;
    //  $premier->setTitle('lo');
    //  $premier->setDescription('loidid');
     
    //  $em->persist($premier);
    //  $em->flush();

      
       $pins = $pinRepository->findAll();
        return $this->render('pins/index.html.twig', [
            'pins' => $pins
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="show")
     */
    public function show(Pin $pin)
    {
           return $this->render('pins/show.html.twig', compact('pin'));
    }


    /**
     * @Route("/pins/create", name="create")
     */
    public function create(EntityManagerInterface $em): Response
    {
     
        return $this->render('pins/create.html.twig'
           
        );
    }
    
}
