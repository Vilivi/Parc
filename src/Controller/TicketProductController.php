<?php

namespace App\Controller;

use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(): Response
    {
        $tickets = $this->em->getRepository(Ticket::class)->findAll();

        return $this->render('ticket_product/index.html.twig', [
            'tickets' => $tickets
        ]);
    }
}
