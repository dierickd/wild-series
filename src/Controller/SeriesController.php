<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    /**
     * @Route("/series", name="wild_show_allSeries")
     * @return Response
     */
    public function index() :Response
    {
        return $this->render('wild/show/allSeries.html.twig');
    }
}