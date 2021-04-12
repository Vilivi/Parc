<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Tools;
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
    public function index(Request $request, Cart $cart, Tools $tools): Response
    {
        $form = $this->createForm(DayType::class);
        $form->handleRequest($request);

        $result = null; 

        if($form->isSubmitted() && $form->isValid()) {
            // je récupère les deux dates et les compare à aujourd'hui
            $start_date = $form->get('start_date')->getData();
            $end_date = $form->get('end_date')->getData();
            $now = new \DateTime("now");

            // si la date de début est supérieur ou égale à aujourd'hui et que la date de fin est supérieur ou égale à celle du début
            // alors les dates sont valides
            if($start_date >= $now  && $end_date >= $start_date){
                if($cart->getTicketsQuantity()) {
                    // si le nombre de ticket nécessaire par jour a déjà été rentré, je vérifie si ces dates disposent de quantités suffisantes
                    $result = $tools->createAlert([$start_date, $end_date], $cart->getTicketsQuantity());
                    if($result == null){
                        $cart->setDays($start_date, $end_date);
                        $this->addFlash('notice', 'Les dates ont bien été enregistrées. Vous pouvez en choisir de nouvelles pour en changer si vous le souhaitez.');
                        return $this->redirectToRoute('cart');
                    } else {
                        $this->addFlash('warning', $result);
                        return $this->redirectToRoute('reservation');
                    }
                } else {
                    // si aucune quantité n'a été enregistré, j'enregistre les dates
                    $cart->setDays($start_date, $end_date);
                    $this->addFlash('notice', 'Les dates ont bien été enregistrées. Vous pouvez en choisir de nouvelles pour en changer si vous le souhaitez.');
                    return $this->redirectToRoute('cart');
                }
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
