<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Form\DayType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    /**
     * @Route("/reserver", name="reservation")
     */
    public function index(Request $request, Cart $cart): Response
    {
        $form = $this->createForm(DayType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $start_date = $form->get('start_date')->getData();
            $end_date = $form->get('end_date')->getData();
            $now = new \DateTime("now");

            // si la date de début est supérieur ou égale à aujourd'hui et que la date de fin est supérieur ou égale à celle du début
            // alors les dates sont valides
            if($start_date >= $now  && $end_date >= $start_date){
                $cart->setDays($start_date, $end_date);
                $this->addFlash('notice', 'Les dates ont bien été enregistrées. Vous pouvez en choisir de nouvelles pour en changer si vous le souhaitez.');
                return $this->redirectToRoute('cart');
            } else if($now > $start_date || $now > $end_date) {
                $this->addFlash('warning','Vous ne pouvez réserver à une date antérieure à aujourd\'hui');
            } else {
                $this->addFlash('warning', 'La date de fin ne peut être ultérieure à la date de début du séjour.');
            }
        } 

        $sejour = null;
        if($cart->getDays()){
            $sejour = true;
        }
        
        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
            'sejour' => $sejour
        ]);
    }

    /**
     * @Route("/reservation-annulee", name="reservation_remove")
     */
    public function remove(Cart $cart): Response
    {
        $sejour = $cart->getDays();
        if($sejour) {
            $cart->removeDays();
        } 
        
        return $this->redirectToRoute('cart');
    }
}
