<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Tools;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart, Tools $tool): Response
    {
        $days = $cart->getDays();
        
        if($days) {
            $duration = $tool->tripDuration($days[0], $days[1]);
        } else {
            $duration = 1;
        }

        return $this->render('cart/index.html.twig', [
            'duration' => $duration,
            'days' => $cart->getDays(),
            'tickets' => $cart->getFullTickets()
        ]);
    }
}
