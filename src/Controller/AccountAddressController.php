<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/mon-compte/mes-adresses", name="account_address")
     */
    public function index(): Response
    {
        $addresses = $this->em->getRepository(Address::class)->findByUser($this->getUser());

        return $this->render('account/address.html.twig', [
            'addresses' => $addresses
        ]);
    }

    /**
     * @Route("/mon-compte/ajouter-une-adresse", name="account_address_add")
     */
    public function add(Request $request): Response
    {
        $address = new Address();
        $address->setUser($this->getUser());

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $this->em->persist($address);
            $this->em->flush();
            $this->addFlash('notice', 'Votre adresse a bien été ajoutée.');
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/modifier-mon-adresse/{id}", name="account_address_edit")
     */
    public function edit($id, Request $request): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser()){
            $this->addFlash('notice', 'Désolé, nous n\'avons pas trouvé l\'adresse demandé. ');
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $this->em->flush();
            $this->addFlash('notice', 'Votre adresse a bien été ajoutée.');
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/supprimer-mon-adresse/{id}", name="account_address_delete")
     */
    public function delete($id): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser()){
            $this->addFlash('notice', 'Désolé, nous n\'avons pas trouvé l\'adresse demandé. ');
            return $this->redirectToRoute('account_address');
        }

        $this->em->remove($address);
        $this->em->flush();
        $this->addFlash('notice', 'L\'adresse a bien été supprimée');

        return $this->redirectToRoute('account_address');
    }
}
