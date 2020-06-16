<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Require ROLE_ADMIN for *every* controller method in this class
 * @IsGranted("ROLE_ADMIN")
 *
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends AbstractController
{

    /**
     * Require ROLE_ADMIN for *every* controller method in this class
     * @IsGranted("ROLE_ADMIN")
     *
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
