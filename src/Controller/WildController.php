<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/wild", name="wild_index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
        );
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
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     * @Route("/wild/category/{categoryName}", name="wild_category")
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName): Response
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($categoryName)]);

        $selectByCategory = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category],
                ['id' => 'DESC'],
                3
            );

        return $this->render('wild/showByCategory.html.twig', [
            'selectByCategory' => $selectByCategory,
        ]);
    }

    /**
     * @Route("/wild/{slug}/season/{id}", name="wild_season")
     * @param int $id
     * @return Response
     */
    public function showBySeason(int $id): Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => $season->getProgram()->getTitle()]);

        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season' => $season]);

        return $this->render('wild/showBySeason.html.twig', [
            'season' => $season,
            'program' => $program,
            'episodes' => $episodes,
        ]);
    }
}