<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("/", name="wild_home")
     * @return Response
     */
    public function index() :Response
    {
        return $this->render('wild/home.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }

}