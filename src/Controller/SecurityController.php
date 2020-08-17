<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            // Affiche un message si l'utilisateur essaye de se connecter alor qu'il est déjà connecté
            $this->addFlash('error', 'Vous êtes déjà connecté');
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(Request $request)
    {


        // return $this->redirectToRoute('home');
    }
    /**
     * @Route("/oubli-password", name="app_forgotten_password")
     */

    public function forgottenPass(Request $request, UserRepository $userRepository, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGeneratorInterface)
    {

        // On cré le formulaire

        $form = $this->createForm(ResetPasswordType::class);

        // On traite le formulaire

        $form->handleRequest($request);

        // Si le formulaire est valide

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données du formulaire
            $donnees = $form->getData();

            // On cherche si un utilisateur a cet email
            $user = $userRepository->findOneByEmail($donnees['email']);

            // Si l'utilisateur n'existe pas
            if (!$user) {
                // On envoie un message flash
                $this->addFlash('danger', 'Cette adresse n\'existe pas');
                $this->redirectToRoute('app_login');
                return $this->redirectToRoute('app_login');
            }
            // On génère un token
            $token = $tokenGeneratorInterface->generateToken();
            try {
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {

                $this->addFlash('warning', 'Une erreur est survenue');
                return $this->redirect('app_login');
            }
            // ON génère l'URL de réinitialisation du mot de passe
            $url = $this->generateUrl(
                'app_reset_password',
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            // On envoie le message
            $message = (new \Swift_Message('Mot de passe oublié'))

                // On attribue l'expéditeur
                ->setFrom('monadresse@gmail.com')
                // On attribue le destinataire
                ->setTo($user->getEmail())


                // On crée le contenu
                ->setBody(
                    "<Une>Bonjour,<br/>Une demande de réinitialisation de mot de passe a été effectuée pour le site PartageTonCode.com.
                   Veuillez cliquer sur le lien suivant:" . $url . " </p>",
                    'text/html'
                );
            // On envoie l'email
            $mailer->send($message);

            // Message flash

            $this->addFlash('message', 'Un e-mail de réinitialisation de votre mot de passe vous a été envoyé');

            return $this->redirectToRoute('app_login');
        }

        // On envoie vers la page de demande de l'email
        return $this->render('security/forgotten_password.html.twig', ['emailForm' => $form->createView()]);
    }
    /**
     * @Route("/reset-pass/{token}", name="app_reset_password")
     */

    public function resetPassword($token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // ON cherche l'utilisateur avec le token fourni
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Token inconnu');
            return $this->redirectToRoute('app_login');
        }

        // Si le formulaire est envoyé en methode POST
        if ($request->isMethod('POST')) {
            $user->setResetToken(null);

            // On chiffre le mot de passe

            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Mot de passe modifié avec succès');

            return $this->redirectToRoute('app_login');
        } else {
            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }
    }
}
