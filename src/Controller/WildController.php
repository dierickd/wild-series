<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     * @return Response
     */
    public function index(): Response
    {
        $categories = $this->selectAllCategories();

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render('wild/index.html.twig', [
            'programs' => $programs,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/wild/show/{slug}", name="wild_show")
     * @param string $slug
     * @return Response
     */
    public function show(string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = str_replace("-", ' ', ucwords(trim(strip_tags($slug)), "-"));
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
        ]);
    }

    /**
     * @Route("/wild/category/{category}", name="wild_category")
     * @param int $category
     * @return Response
     */
    public function category(int $category): Response
    {
        if (!$category) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }
        $categories = $this->selectAllCategories();

        $categoryActive = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['id' => $category]);

        $selectByCategory = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findByCategory($category);

        return $this->render('wild/showByCategory.html.twig', [
            'selectByCategory' => $selectByCategory,
            'categories' => $categories,
            'categoryActive' => $categoryActive,
        ]);
    }

    /**
     * @return array
     */
    public function selectAllCategories(): array
    {
        return $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
    }
}