<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @Route("/wild/show/{slug<[a-zA-Z-]+[0-9]*>}", name="wild_show")
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
            'seasons' => $program->getSeasons(),
        ]);
    }

    /**
     * @Route("/wild/category/{categoryName<[a-zA-Z-]+>}", name="wild_category")
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
     * @Route("/wild/{slug<[a-zA-Z-]+[0-9]*>}/season/{id<[0-9]+>}", name="wild_show_season")
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

        return $this->render('wild/showBySeason.html.twig', [
            'season' => $season,
            'program' => $season->getProgram(),
            'episodes' => $season->getEpisodes(),
        ]);
    }

    /**
     * @Route("/wild/{slug<[a-zA-Z-]+[0-9]*>}/season/{season_number<[0-9]+>}/episode/{id<[0-9]+>}", name="wild_show_episode")
     * @ParamConverter("episode", options={"mapping": {"id": "id"}})
     * @param int     $season_number
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(int $season_number, Episode $episode): Response
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $season_number]);

        return $this->render('wild/show/episode.html.twig',
            [
                'episode' => $episode,
                'season' => $episode->getSeason(),
                'program' => $season->getProgram(),
            ]);
    }
}