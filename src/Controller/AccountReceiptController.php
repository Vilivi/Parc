<?php

namespace App\Controller;

use App\Classe\Tools;
use App\Entity\Receipt;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountReceiptController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/mon-compte/mes-commandes", name="account_receipt")
     */
    public function index(): Response
    {
        $receipts = $this->em->getRepository(Receipt::class)->findSuccessReceipts($this->getUser());
        $empty = true;

        if($receipts) {
            $empty = false;
        }

        return $this->render('account/receipts.html.twig', [
            'empty' => $empty, 
            'receipts' => $receipts
        ]);
    }

    /**
     * @Route("/mon-compte/ma-commande/{reference}", name="account_receipt_show")
     */
    public function show($reference, Tools $tools): Response
    {
        $receipt = $this->em->getRepository(Receipt::class)->findOneByReference($reference);

        if(!$receipt) {
            $this->addFlash('warning', 'Nous n\'avons pas pu trouver la commande demandée');
            return $this->redirectToRoute('account_receipt');
        }

        $isNotPassed = $tools->checkTicketIsNotPassed($receipt->getDays());
        
        return $this->render('account/show_receipt.html.twig', [
            'receipt' => $receipt, 
            'reference' => $reference,
            'isNotPassed' => $isNotPassed
        ]);
    }

    /**
     * @Route("/mon-compte/mon-billet/{reference}", name="account_ticket_show")
     */
    public function showTicket($reference): Response
    {
        $receipt = $this->em->getRepository(Receipt::class)->findOneByReference($reference);

        if(!$receipt) {
            $this->addFlash('warning', 'Nous n\'avons pas pu trouver la commande demandée');
            return $this->redirectToRoute('account_receipt');
        }

        $days = $receipt->getDays()->getValues();

        $products = [];
        $quantity = [];
        foreach($receipt->getOrderDetails() as $orderDetails) {
            $products []= $orderDetails->getProduct();
        }
        foreach($receipt->getOrderDetails() as $orderDetails) {
            $quantity []= $orderDetails->getQuantity() / count($days);
        }

        $productsAndQuantity = [$products, $quantity]; 

        return $this->render('account/ticket.html.twig', [
            'reference' => $reference,
            'productsAndQuantity' => $productsAndQuantity 
        ]);
    }
    
    /**
     * @Route("/mon-compte/telecharger-mon-billet/{reference}", name="account_download_ticket")
     */
    public function downloadTicket($reference): Response
    {
        $receipt = $this->em->getRepository(Receipt::class)->findOneByReference($reference);

        if(!$receipt) {
            $this->addFlash('warning', 'Nous n\'avons pas pu trouver la commande associée.');
            return $this->redirectToRoute('account_receipt');
        }

        $days = $receipt->getDays()->getValues();

        $products = [];
        $quantity = [];
        foreach($receipt->getOrderDetails() as $orderDetails) {
            $products []= $orderDetails->getProduct();
        }
        foreach($receipt->getOrderDetails() as $orderDetails) {
            $quantity []= $orderDetails->getQuantity() / count($days);
        }

        $productsAndQuantity = [$products, $quantity];

        $dompdf = new Dompdf();
        $html = $this->renderView('account/pdf.html.twig', [
            "reference" => $reference,
            "productsAndQuantity" => $productsAndQuantity,
            "days" => $days
        ]); 
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $this->render('account/ticket_dompdf.html.twig', [
            'pdf' => $dompdf->stream(),
        ]);
    }
}