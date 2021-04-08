<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/mon-compte/modifier-mon-mot-de-passe", name="password")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        $notification = null; 

        if($form->isSubmitted() && $form->isValid()){
            $old_pwd = $form->get('old_password')->getData();

            // si le mot de passe actuel est le bon 
            if($encoder->isPasswordValid($user, $old_pwd)) {
                //on encode le nouveau mot de passe et on le set à l'utilisateur
                $password = $encoder->encodePassword($user, $form->get('new_password')->getData());
                $user->setPassword($password);
                // on flush
                $this->em->flush();
                // on affiche une notification
                $this->addFlash('notice', 'Votre mot de passe a bien été modifié');
                // on envoie un mail à l'utilisateur
                // on redirige vers account
                return $this->redirectToRoute('account');
            //sinon on envoie une notification de mauvais mot de passe
            } else {
                $this->addFlash('notice', 'Le mot de passe actuel n\'est pas le bon.');
                return $this->render('account/password.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
