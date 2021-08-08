<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User(); 
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        $notification = null;

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            
            $search_email = $this->em->getRepository(User::class)->findOneByEmail($user->getEmail());
            $search_pseudo = $this->em->getRepository(User::class)->findOneByPseudo($user->getPseudo());

            if($search_email){
                $notification[] = "L'email est déjà utilisé.";
                if($search_pseudo){
                    $notification[] .= "Le pseudo est déjà utilisé.";
                }
            } else if ($search_pseudo){
                $notification[] = "Le pseudo est déjà utilisé.";
            } else {
                $pass = $user->getPassword();
                $password = $encoder->encodePassword($user,$user->getPassword());
                $user->setPassword($password);

                $this->em->persist($user);
                $this->em->flush();
                
                $mail = new Mail();
                $content = "Bonjour " . $user->getFirstName() . ", <br> Bienvenue sur Hoa Mai Parc! <br> Votre compte a bien été créé. <br> <br> Votre mot de passe est : " . $pass;
                $mail->send($user->getEmail(), $user->getFirstName(), "Modification de votre mot de passe", $content);
                $notification[] = "Votre inscription s'est très bien déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notifications' => $notification
        ]);
    }
}
