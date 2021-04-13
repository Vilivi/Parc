<?php 

namespace App\Classe;

use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart 
{
    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->entityManager = $em;
    }

    public function setDays(\Datetime $start, \Datetime $end)
    {
        $this->setTripDuration($start, $end);
        $this->session->set('days', [$start, $end]);
    }

    public function getDays()
    {
        return $this->session->get('days');
    }

    private function setTripDuration(\Datetime $start, \Datetime $end)
    {
        $start = $start->format('d-m-Y');
        $end = $end->format('d-m-Y');
        $duration = (strtotime($end) - strtotime($start))/(60*60*24)+1; 

        $this->session->set('duration', $duration);
    }

    public function getTripDuration()
    {
        return $this->session->get('duration'); 
    }

    public function removeDays()
    {
        return $this->session->remove('days');
    }

    public function setFullTickets($q1, $q2, $q3) 
    {
        $this->removeCartTickets();

        for($i = 0; $i < $q1; $i+= 1) {
            $this->addTicket(1);
        }
        for($i = 0; $i < $q2; $i+= 1) {
            $this->addTicket(2);
        }
        for($i = 0; $i < $q3; $i+= 1) {
            $this->addTicket(3);
        }
    }

    public function addTicket($id)
    {
        // on prend le panier, on le modifie, et on set le panier avec les nouvelles valeurs
        $cart_ticket = $this->session->get('cart_ticket', []);

        if (!empty($cart_ticket[$id])) {
            $cart_ticket[$id]++;
        } else {
            $cart_ticket[$id] = 1;
        }

        $this->session->set('cart_ticket', $cart_ticket);
    }

    public function decreaseTicket($id)
    {
        // on prend le panier, on le modifie, et on set le panier avec les nouvelles valeurs
        $cart_ticket = $this->session->get('cart_ticket', []);

        if ($cart_ticket[$id] > 1) {
            $cart_ticket[$id]--;
        } else {
            unset($cart_ticket[$id]);
        }

        $this->session->set('cart_ticket', $cart_ticket);
    }

    public function deleteTickets($id)
    {
        $cart_ticket = $this->session->get('cart_ticket', []);

        unset($cart_ticket[$id]);

        return $this->session->set('cart_ticket', $cart_ticket);
    }

    public function removeCartTickets()
    {
        return $this->session->remove('cart_ticket');
    }

    public function getCartTicket()
    {
        return $this->session->get('cart_ticket');
    }

    public function getFullTickets()
    {
        $cartFullTickets = [];
        
        if($this->getCartTicket()){
            foreach($this->getCartTicket() as $id => $quantity){
                //Si l'id rentré ne correspond pas à un ticket, on le supprime de la session avant de continuer
                $ticket = $this->entityManager->getRepository(Ticket::class)->findOneById($id);

                if (!$ticket){
                    $this->deleteTickets($id);
                    continue;
                }

                $cartFullTickets[] = [
                    'ticket' => $this->entityManager->getRepository(Ticket::class)->findOneById($id), 
                    'quantity' => $quantity
                ];
            }
        }

        return $cartFullTickets;
    }

    public function getTicketsQuantity()
    {
        $tickets = $this->getFullTickets();
        $quantity = 0;
        foreach($tickets as $ticket){
            $quantity += $ticket['quantity'];
        }
        return $quantity;
    }

    // public function getCartOrder()
    // {
    //     $order = ['tickets' => $this->getFullTickets(), $this->getTripDuration()];
    //     return $order;
    // }
}