<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    /**
     * @Route("/movies", name="wild_show_allMovies")
     * @return Response
     */
    public function index() :Response
    {
        return $this->render('wild/show/allMovies.html.twig');
    }
}