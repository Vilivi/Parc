<?php

namespace App\Controller;

use App\Entity\Attraction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AttractionController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/attractions", name="attraction")
     */
    public function index(): Response
    {
        $attractions = $this->em->getRepository(Attraction::class)->findAll();

        return $this->render('attraction/index.html.twig', [
            'attractions' => $attractions
        ]);
    }

    /**
     * @Route("/attraction/{slug}", name="attraction_show")
     */
    public function show($slug): Response
    {
        $attraction = $this->em->getRepository(Attraction::class)->findOneBySlug($slug);

        return $this->render('attraction/show.html.twig', [
            'attraction' => $attraction
        ]);
    }
}
