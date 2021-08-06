<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Receipt;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index($reference)
    {
        $YOUR_DOMAIN = $_ENV['DOMAIN_URL'];
        $products_for_stripe = [];

        $receipt = $this->em->getRepository(Receipt::class)->findOneByReference($reference);

        if(!$receipt) {
            new JsonResponse(['error' => 'error']);
        }
        
        foreach($receipt->getOrderDetails()->getValues() as $product) {
            $product_object = $this->em->getRepository(Ticket::class)->findOneByTicketName($product->getProduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product_object->getTicketPrice(),
                    'product_data' => [
                        'name' => $product_object->getTicketName(),
                    ]],
                    'quantity' => $product->getQuantity(),
            ];
        }

        Stripe::setApiKey($_ENV['KEY_STRIPE']);

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $products_for_stripe
        ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $receipt->setStripeSessionId($checkout_session->id);
        $this->em->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="stripe_success")
     */
    public function success($stripeSessionId, Cart $cart)
    {
        $receipt = $this->em->getRepository(Receipt::class)->findOneByStripeSessionId($stripeSessionId);

        $days = $receipt->getDays()->getValues();
        foreach($days as $day) {
            // pour diminuer les tickets restants en fonction des reservations
            $remainingTickets = $day->getRemainingTickets() - $cart->getTicketsQuantity();
            $day->setRemainingTickets($remainingTickets);
            $this->em->flush();
        }

        if(!$receipt || $receipt->getUser() != $this->getUser()){
            //si la commande n'existe pas ou si elle ne correspond pas aux commandes de l'utilisateur connecté
            return $this->redirectToRoute('home');
        }

        // modifier le status state à 1 maintenant que le paiement a bien été reçu.
        // vider la session cart
        if($receipt->getState() == 0){
            $receipt->setState(1);
            $this->em->flush();
            $cart->removeCart();
        }
        //envoyer un mail au client pour lui confirmer sa commande.
        // $mail = new Mail();
        // $content = "Bonjour ". $receipt->getUser()->getFirstName() .", <hr> merci pour ta commande sur notre site Hoa Mai Parc"; 
        // $mail->send($receipt->getUser()->getEmail(), $receipt->getUser()->getFirstName(), "Votre commande est bien validée", $content);
        
        
        //afficher les informations de la commande de l'utilisateur.
        
        return $this->render('stripe/success.html.twig', [
            'receipt' => $receipt,
            'orderDetails' => $receipt->getOrderDetails()->getValues(), 
            'days' => $receipt->getDays()->getValues()
        ]);
    }

    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="stripe_canceled")
     */
    public function canceled() 
    {
        return $this->render('stripe/canceled.html.twig');
    }
}
