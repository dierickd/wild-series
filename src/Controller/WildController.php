<?php


namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Episode;
use App\Repository\ActorRepository;
use Doctrine\ORM\Query;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProgramSearchType;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/series", name="wild_index")
     * @param ProgramRepository  $programRepository
     * @param PaginatorInterface $paginator
     * @param Request            $request
     * @return Response A response instance
     */
    public function index(
        ProgramRepository $programRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $programs = $paginator->paginate(
            $programRepository->findAllPrograms(),
            $request->query->getInt('page', 1),
            10
        );

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        $form = $this->createForm(ProgramSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $programs = $paginator->paginate(
                $programRepository->search(mb_strtolower($data['searchField'])),
                $request->query->getInt('page', 1),
                10
            );
        }

        return $this->render(
            'wild/index.html.twig',
            [
                'programs' => $programs,
                'form' => $form->createView(),
                'categories' => $this->getCategory(),
            ]
        );
    }

    /**
     * @Route("/series/{slug<[a-zA-Z-:]+[0-9]*>}", name="wild_show")
     * @param string            $slug
     * @param ProgramRepository $programRepository
     * @return Response
     */
    public function show(string $slug, ProgramRepository $programRepository): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('Le programme recherché n\a pas été trouvé !');
        }
        $slug = str_replace("-", ' ', ucwords(trim(strip_tags($slug)), "-"));
        $program = $programRepository->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException(
                'La série \'' . $slug . '\' n\'existe pas ou a été supprimé.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'seasons' => $program->getSeasons(),
        ]);
    }

    /**
     * @Route("/series/category/{categoryName<[a-zA-Z-]+>}", name="wild_category")
     * @param string             $categoryName
     * @param CategoryRepository $categoryRepository
     * @param ProgramRepository  $programRepository
     * @return Response
     */
    public function showByCategory(
        string $categoryName,
        CategoryRepository $categoryRepository,
        ProgramRepository $programRepository
    ): Response
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }

        $category = $categoryRepository->findOneBy(
            ['name' => mb_strtolower($categoryName)]
        );

        if (!$category) {
            throw $this->createNotFoundException(
                'La catégorie n\'existe pas ou a été supprimée.'
            );
        }

        $selectByCategory = $programRepository->findBy(
            ['category' => $category]
        );

        if (!$category) {
            throw $this->createNotFoundException(
                'La catégorie n\'existe pas ou a été supprimée.'
            );
        }

        return $this->render('wild/showByCategory.html.twig', [
            'selectByCategory' => $selectByCategory,
            'categories' => $this->getCategory()
        ]);
    }

    /**
     * @Route("/series/{slug<[a-zA-Z-:]+[0-9]*>}/{id<[0-9]+>}/season/{number<[0-9]+>}", name="wild_show_season")
     * @param int                $id
     * @param SeasonRepository   $seasonRepository
     * @param Request            $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function showBySeason(int $id, SeasonRepository $seasonRepository, Request $request, PaginatorInterface $paginator): Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }

        $season = $seasonRepository->findOneBy(['id' => $id]);

        return $this->render('wild/showBySeason.html.twig', [
            'season' => $season,
            'program' => $season->getProgram(),
            'episodes' => $paginator->paginate(
                $season->getEpisodes(),
                $request->query->getInt('page', 1),
                10
            ),
            'categories' => $this->getCategory()
        ]);
    }

    /**
     * @Route("/series/{season_id<[0-9]+>}/{id<[0-9]+>}/{slug<[a-zA-Z-]+[0-9]*>}", name="wild_show_episode")
     * @ParamConverter("episode", options={"mapping": {"id": "id"}})
     * @param int              $season_id
     * @param Episode          $episode
     * @param SeasonRepository $seasonRepository
     * @return Response
     */
    public function showEpisode(
        int $season_id,
        Episode $episode,
        SeasonRepository $seasonRepository
    ): Response
    {
        $season = $seasonRepository->findOneBy(['id' => $season_id]);

        return $this->render('wild/show/episode.html.twig',
            [
                'episode' => $episode,
                'season' => $episode->getSeason(),
                'program' => $season->getProgram(),
                'categories' => $this->getCategory()
            ]);
    }

    /**
     * @Route("/actor/{id}/{slug<[a-zA-Z-]+>}", name="wild_actor")
     * @param Actor $actor
     * @return Response
     */
    public function showByActor(Actor $actor): Response
    {
        return $this->render('wild/showByActor.html.twig', [
            'actor' => $actor,
        ]);
    }

    /**
     * @Route("/wild/actors", name="wild_all_actors")
     * @param ActorRepository    $actorRepository
     * @param CategoryRepository $categoryRepository
     * @param PaginatorInterface $paginator
     * @param Request            $request
     * @return Response
     */
    public function showAllActor(
        ActorRepository $actorRepository,
        CategoryRepository $categoryRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        return $this->render('wild/showAllActors.html.twig', [
            'actors' => $paginator->paginate(
                $actorRepository->findAll(),
                $request->query->getInt('page', 1),
                10
            ),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @return Category[]
     */
    public function getCategory()
    {
        return $this->getDoctrine()->getRepository(Category::class)->findAll(['name' => 'ASC']);
    }
}
