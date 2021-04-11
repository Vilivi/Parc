<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Tools;
use App\Form\QuantityTicketsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-compte/mon-panier", name="cart")
     */
    public function index(Cart $cart, Tools $tools, Request $request): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('login_app');
        }

        $days = $cart->getDays();
        if($days) {
            $duration = $tools->tripDuration($days[0], $days[1]);
        } else {
            $duration = 1;
        }

        $form = $this->createForm(QuantityTicketsType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $result = $tools->askquantityTickets($form, $cart);
            if($result == false){
                return $this->redirectToRoute('reservation');
            }
        }

        return $this->render('cart/index.html.twig', [
            'duration' => $duration,
            'days' => $cart->getDays(),
            'tickets' => $cart->getFullTickets(), 
            'form' => $form->createView(),
        ]);
    }
}
