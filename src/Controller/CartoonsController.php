<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CartoonsController extends AbstractController
{
    /**
     * @Route("/cartoons", name="wild_show_allCartoons")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('wild/show/allCartoons.html.twig');
    }
}