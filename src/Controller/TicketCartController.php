<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketCartController extends AbstractController
{
    /**
     * @Route("/cart/addTicket/{id}", name="ticket_cart_add")
     */
    public function addTicketToCart($id, Cart $cart): Response
    {
        $cart->addTicket($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/decreaseTicket/{id}", name="ticket_cart_decrease")
     */
    public function decreaseTicketToCart($id, Cart $cart): Response
    {
        $cart->decreaseTicket($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/deleteTickets/{id}", name="ticket_cart_delete")
     */
    public function deleteTicketsToCart($id, Cart $cart): Response
    {
        $cart->deleteTickets($id);
        return $this->redirectToRoute('cart');
    }
}
