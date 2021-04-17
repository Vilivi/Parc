<?php

namespace App\Controller;

use App\Entity\Receipt;
use Doctrine\ORM\EntityManagerInterface;
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
        $receipts = $this->em->getRepository(Receipt::class)->findByUser($this->getUser());
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
    public function show($reference): Response
    {
        $receipt = $this->em->getRepository(Receipt::class)->findOneByReference($reference);

        if(!$receipt) {
            $this->addFlash('warning', 'Nous n\'avons pas pu trouver la commande demandée');
            return $this->redirectToRoute('account_receipt');
        }

        return $this->render('account/show_receipt.html.twig', [
            'receipt' => $receipt
        ]);
    }
}
