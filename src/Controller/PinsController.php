<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{
    // pour ne pas à avoir à appeler entityManagerInterface à chaque function
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }
    /**
     
     * @Route("/", name="home",methods="GET")
     */
    public function index(EntityManagerInterface $em, PinRepository $pinRepository)
    {
     
    //Ajouter un pin manuellement

    //  $premier = new Pin;
    // $premier->setTitle('pin4');
    // $premier->setDescription('pin 4');
     
     //$em->persist($premier);
     // $em->flush();

      
       $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', [
            'pins' => $pins
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="show",methods="GET")
     */
    public function show(Pin $pin)
    {
           return $this->render('pins/show.html.twig', compact('pin'));
    }

     /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="edit",methods="GET|POST")
     */
    public function edit(Request $request,Pin $pin,EntityManagerInterface $em):response
    {
        $form = $this->createFormBuilder($pin)
        ->add('title', TextType::class)
        ->add('description', TextareaType::class)
        
        ->getForm();
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()){
      
            
          
            // pour récupérer entité manager si pas d'injection de dépendance
            // $em = $this->getDoctrine()->getManager();
           
            $em->flush();
            return $this->redirectToRoute('home');
        }
           return $this->render('pins/edit.html.twig',[
               'pin' => $pin,
               'form' => $form->createView()
           ]);
    }

    /**
     * @Route("/pins/create", name="create",methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
     $form = $this->createFormBuilder(new Pin)
    ->add('title', TextType::class)
    ->add('description', TextareaType::class)
    
    ->getForm();

    $form->handleRequest($request);
    if( $form->isSubmitted() && $form->isValid()){
      
        $pin = $form->getData();
      
        // pour récupérer entité manager si pas d'injection de dépendance
        // $em = $this->getDoctrine()->getManager();
        $em->persist($pin);
        $em->flush();
        return $this->redirectToRoute('home');
    }
        return $this->render('pins/create.html.twig',[
        'form' => $form->createView()
           
        ]);
    }
    
}
