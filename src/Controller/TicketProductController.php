<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Tools;
use App\Entity\Ticket;
use App\Form\QuantityTicketsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketProductController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em =$em;
    }

    /**
     * @Route("/tarifs", name="ticket_product")
     */
    public function index(Cart $cart, Request $request, Tools $tools): Response
    {
        $tickets = $this->em->getRepository(Ticket::class)->findAll();

        $form = $this->createForm(QuantityTicketsType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $result = $tools->askquantityTickets($form, $cart);
            if($result == false){
                return $this->redirectToRoute('reservation');
            }
        }

        return $this->render('ticket_product/index.html.twig', [
            'tickets' => $tickets,
            'days' => $cart->getDays(),
            'form' => $form->createView()
        ]);
    }
}
