<?php 

namespace App\Classe;

use App\Entity\Day;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

Class Tools extends AbstractController 
{
    
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function retrieveDatesTrip($days)
    {
        for($i = strtotime($days[0]->format('d-m-Y')); $i <= strtotime($days[1]->format('d-m-Y')); $i+=86400) {
            $dates []= date("d-m-Y",$i);
        }
        return $dates;
    }

    public function searchDatesTrip($days)
    {
        $dates = $this->retrieveDatesTrip($days);
        $results = null;
        foreach($dates as $date)
        {
            $results []= $this->em->getRepository(Day::class)->findOneByDate($date);
        }

        return $results;
    }

    public function checkDisponibilities(array $dates, int $quantity)
    {
        // je récupère toutes les dates entre la date de début et de fin du séjour
        $dates = $this->retrieveDatesTrip($dates);

        // pour chaque date, je récupère les données de la bdd ou je crée l'objet
        // ensuite, pour chaque objet je récupère les tickets restants et je les compare à la quantité
        // si c'est bon, j'insère true dans results sinon j'insère la date pour pouvoir créer une alert à l'utilisateur
        foreach($dates as $date)
        {
            if(!$this->em->getRepository(Day::class)->findOneByDate($date)) {
                $day = new Day();
                $day->setDate($date);
                $this->em->persist($day);
                $this->em->flush();
            } else {
                $day = $this->em->getRepository(Day::class)->findOneByDate($date);
            }

            $results []= ($day->getRemainingTickets() >= $quantity) ? true : $day->getDate();
        }
        return $results;
    }

    public function createAlert(array $dates, int $quantity)
    {
        $results = $this->checkDisponibilities($dates, $quantity);
        $nonValidDates = [];
        $alert = "Nous sommes désolés, le nombre de place disponibles pour les dates suivantes n'est pas suffisant : ";
        foreach($results as $result)
        {
            if($result !== true){
                $nonValidDates []= $result;
            }
        }

        if(count($nonValidDates) >= 1){
            $alert .= implode(", ",$nonValidDates);
            return $alert .= '.';
        } else {
            return null;
        }
    }

    public function askquantityTickets($form, Cart $cart)
    {
        $q1 = $form['1'];
        $q2 = $form['2'];
        $q3 = $form['3'];
            
        $days = $cart->getDays();
        if($days) {
            $alert = $this->createAlert($days, $cart->getTicketsQuantity());
            if($alert !== null){
                $cart->removeCartTickets();
                $this->addFlash('warning', $alert);
                return false;
            } else {
                $cart->setFullTickets($q1, $q2, $q3);
                return $this->redirectToRoute('cart');;
            }
        } else {
            // s'il n'y a pas encore eu de réservation, j'enregistre les quantités et redirige vers la réservation des dates du séjour
            $cart->setFullTickets($q1, $q2, $q3);
            return $this->redirectToRoute('reservation');
        }
    }
}