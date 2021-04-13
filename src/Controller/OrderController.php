<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Tools;
use App\Entity\OrderDetail;
use App\Entity\Receipt;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/ma-commande", name="order")
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

        $form = $this->createForm(OrderType::class, null, [
            'user' =>$this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ma-commande/recapitulatif", name="order_recap")
     */
    public function addCart(Cart $cart, Tools $tools, Request $request): Response
    {
        if(!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }

        if($cart->getTripDuration()) {
            $duration = $cart->getTripDuration();
        } else if (!$cart->getDays()) {
            return $this->redirectToRoute('reservation');
        }
        
        $form = $this->createForm(OrderType::class, null, [
            'user' =>$this->getUser()
        ]);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $date = new DateTime();
            $reference = $date->format('dmY'). '-' . uniqid();
            $billindAddress = $form->get('addresses')->getData();
            $billindAddressContent = $billindAddress->getFirstName(). ' ' . $billindAddress->getLastName();

            if($billindAddress->getCompany()){
                $billindAddressContent .= '<br/>'. $billindAddress->getCompany();
            }

            $billindAddressContent .= '<br/>' . $billindAddress->getAddress();
            $billindAddressContent .= '<br/>' . $billindAddress->getPostal() . ' ' . $billindAddress->getCity();
            $billindAddressContent .= '<br/>' . $billindAddress->getCountry();
            
            $receipt = new Receipt();
            $receipt->setReference($reference);
            $receipt->setCreatedAt($date);
            $receipt->setBillingAddress($billindAddressContent);
            $receipt->setUser($this->getUser());
            $receipt->setState(0);
            $days = $tools->searchDatesTrip($cart->getDays());
            foreach($days as $day) {
                $receipt->addDay($day);
            }

            $this->em->persist($receipt); 

            foreach($cart->getFullTickets() as $ticket) {
                $orderDetailTicket = new OrderDetail();
                $orderDetailTicket->setProduct($ticket['ticket']->getTicketName());
                $orderDetailTicket->setQuantity($ticket['quantity']);
                $orderDetailTicket->setPrice($ticket['ticket']->getTicketPrice());
                $orderDetailTicket->setTotal($ticket['ticket']->getTicketPrice() * $ticket['quantity']);
                $orderDetailTicket->setReceipt($receipt);
                $this->em->persist($orderDetailTicket);
            }
            // $this->em->flush();

            return $this->render('order/add.html.twig', [
                'duration' => $duration,
                'tickets' => $cart->getFullTickets(),
                'billingAddress' => $receipt->getBillingAddress(),
                'reference' => $receipt->getReference()
            ]);
        }

        return $this->redirectToRoute('cart');        
    }
}
