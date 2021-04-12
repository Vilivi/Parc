<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/mon-compte/ma-commande", name="order")
     */
    public function index(Cart $cart): Response
    {
        // si un séjour ou des quantités n'ont pas été enregistré, je redirige vers le premier ou le second en fonction
        if(!$cart->getDays()) {
            return $this->redirectToRoute('reservation');
        }

        if(!$cart->getTicketsQuantity()) {
            return $this->redirectToRoute('ticket_products');
        }

        if(!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('account_address_add');
        }
        
        dd($this->getUser()->getAddresses()->getValues());
        return $this->render('order/index.html.twig');
    }
}
