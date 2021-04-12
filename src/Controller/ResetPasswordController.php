<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request): Response
    {
        if ($this->getUser()){
            return $this->redirectToRoute('home');
        }

        if ($request->get('email')){
            $user = $this->em->getRepository(User::class)->findOneByEmail($request->get('email'));
            
            if($user){
                // enregistrer en bdd la demande de resetPassword avec user, token et createdAt
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $this->em->persist($reset_password);
                $this->em->flush();
                // j'envoie un mail avec le token dans le lien
                $mail = new Mail();
                $url = "http://127.0.0.1:8000";
                $url .= $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                    ]);
                $content = "Bonjour ". $user->getFirstName(). ", <br/> Vous avez demandé à réinitialiser votre mot de passe sur le site Hoa Mai Parc. <br><br>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."' >mettre à jour votre mot de passe</a>.<br> Attention, ce lien ne sera disponible que durant 3 heures.";
                $mail->send($user->getEmail(), $user->getFirstName(), "Réinitialisation de votre mot de passe.", $content);
                // je mets une notification
                $this->addFlash('notice', 'Votre demande de réinitialisation de mot de passe a bien été prise en compte.');
            } else {
                $this->addFlash('notice', 'Cette adresse email n\'a pas de compte associé.');
            }
        }
        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/mot-de-passe-oublie/{token}", name="update_password")
     */
    public function update($token, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $reset_password = $this->em->getRepository(ResetPassword::class)->findOneByToken($token);
        // on vérifie que le token existe et que l'utilisateur n'est pas déjà connecté
        if(!$reset_password || $this->getUser()) {
            $this->redirectToRoute('home');
        }

        // on vérifie que le token n'est pas périmé
        $now = new \DateTime();

        if($now > $reset_password->getCreatedAt()->modify('+ 3 hour')){
            // si oui addFlash et redirection vers la réinitialisation vers reset_password
            $this->addFlash('notice', 'Votre demande de mot de passe a expiré. Merci de la renouveler.');
            return $this->redirectToRoute('reset_password');
        }

        // si non on récupère les donnes du formulaire, on set le nouveau mot de passe encodé
        // on met une notification et on redirige vers la page de connexion
        $user = $reset_password->getUser();
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $new_password = $form->get('new_password')->getData();
            $user->setPassword($encoder->encodePassword($user, $new_password));
            $this->em->flush();
            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
