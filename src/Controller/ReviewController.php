<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Form\ReviewUserType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/avis", name="review")
     */
    public function index(Request $request): Response
    {
        $reviews = $this->em->getRepository(Review::class)->findAll();
        $review = new Review();

        if($this->getUser()) {
            $form = $this->createForm(ReviewUserType::class, $review);
        } else {
            $form = $this->createForm(ReviewType::class, $review);
        }
        $form->handleRequest($request);

        if($form-> isSubmitted() && $form->isValid()) {
            $review->setCreatedAt(new DateTime());
            $review = $form->getData();

            if($this->getUser()) {
                $pseudo = $this->getUser()->getPseudo();
                $review->setPseudo($pseudo);
            }
            
            $this->em->persist($review);
            $this->em->flush();

            return $this->render('review/index.html.twig', [
                'reviews' => $reviews, 
                'form' => $form->createView()
            ]);
        }

        $average = 0;
        foreach($reviews as $review) {
            $average += $review->getNotation();
        }

        if(count($reviews) !== 0) {
            $average /= count($reviews);
        }

        return $this->render('review/index.html.twig', [
            'reviews' => $reviews, 
            'form' => $form->createView(),
            'average' => $average
        ]);
    }
}
