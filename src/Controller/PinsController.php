<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Entity\User;
use App\Form\PinType;
use App\Form\SearchPinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PinsController extends AbstractController
{
    // pour ne pas à avoir à appeler entityManagerInterface à chaque function
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     
     * @Route("/", name="home",methods="GET|POST")
     */
    //  public function index(Request $request,PaginatorInterface $paginator, EntityManagerInterface $em, PinRepository $pinRepository,UserRepository $userRepository)
    //  {

    //Ajouter un pin manuellement

    //  $premier = new Pin;
    // $premier->setTitle('pin4');
    // $premier->setDescription('pin 4');

    //$em->persist($premier);
    // $em->flush();



    //   $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
    //   $user = $userRepository->findAll();

    // Search
    //$form = $this->createForm(SearchPinType::class);
    //$search = $form->handleRequest($request);

    //if($form->isSubmitted() && $form->isValid()){
    //on recherche les annonces correspondant aux mots-clés
    //  $pins = $pinRepository->search($search->get('mots')
    //  ->getData());
    //}
    //    $pins = $paginator->paginate(
    //        $pins, // On passe les données
    //        $request->query->getInt('page', 1),//Numéro de la page en cours
    //        2//Nombre d'article par page
    //    );

    //     return $this->render('pins/index.html.twig', [
    //         'pins' => $pins,
    //         'user' => $user,
    //         'form' =>$form->createView()
    //    ]);
    // }
    public function index(Request $request, PinRepository $repo, PaginatorInterface $paginator)
    {

        $form = $this->createForm(SearchPinType::class);
        $search = $form->handleRequest($request);

        $pins = $repo->findBy([], ['createdAt' => 'DESC']);

        if ($form->isSubmitted() && $form->isValid()) {

            $pins = $repo->search($search->get('mots')
                ->getData());


            if ($pins == null) {
                $pins = $repo->findAll();
                $this->addFlash('error', 'Aucun article contenant ce mot clé dans le titre n\'a été trouvé, essayez en un autre.');
            }
        }

        $pins = $paginator->paginate(
            $pins, // On passe les données
            $request->query->getInt('page', 1), //Numéro de la page en cours
            6 //Nombre d'article par page
        );

        return $this->render('pins/index.html.twig', [
            'pins' => $pins,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/pins/{slug}/edit", name="edit",methods="GET|PUT")
     */
    public function edit(Request $request, Pin $pin, EntityManagerInterface $em): response
    {
        //Création du form manuellement
        // $form = $this->createFormBuilder($pin)
        // ->add('title', TextType::class)
        // ->add('description', TextareaType::class)

        // ->getForm();

        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'

        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            // pour récupérer entité manager si pas d'injection de dépendance
            // $em = $this->getDoctrine()->getManager();

            $em->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/pins/create", name="create",methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);

        //Plus besoin si on passe par la création du form avec createform et non createFormBuilder
        // ->add('title', TextType::class)
        //->add('description', TextareaType::class)

        // ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $pin->setUser($user);
            //$pin = $form->getData();

            // pour récupérer entité manager si pas d'injection de dépendance
            // $em = $this->getDoctrine()->getManager();
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin ajouté avec success');
            return $this->redirectToRoute('home');
        }
        return $this->render('pins/create.html.twig', [
            'form' => $form->createView()

        ]);
    }
        /**
     * @Route("/pins/{slug}", name="show",methods="GET")
     * @ParamConverter("pins")
     */
    public function show(Pin $pin)
    {
        $this->generateUrl('show', array('slug' => $pin->getSlug()), UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/{slug}/delete", name="delete",methods="DELETE")
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): response
    {
        //Affichage du token
        //  dd($request->request->get('csrf_token'));

        //si le token est valid supression du pin
        if ($this->isCsrfTokenValid('pins.delete' . $pin->getId(), $request->request->get('csrf_token'))) {
            $em->remove($pin);
            $em->flush();
            $this->addFlash('success', 'Pin supprimé success');
        }


        return $this->redirectToRoute('home');
    }
}
