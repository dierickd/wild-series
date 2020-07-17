<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Service\Flash;
use App\Service\GetCategory;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProgramSearchType;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Repository\EpisodeRepository;
use App\Repository\SeasonRepository;
use App\Repository\ActorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property array categories
 */
class WildController extends AbstractController
{

    public function __construct(GetCategory $category)
    {
        $this->categories = $category->getCategory();
    }

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
        $isSearch = false;
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

        if ($form->isSubmitted() and $form->isValid()) {
            $data = $form->getData();
            $programs = $paginator->paginate(
                $programRepository->search(mb_strtolower($data['searchField'])),
                $request->query->getInt('page', 1),
                10
            );
            $isSearch = true;
        }

        return $this->render(
            'wild/index.html.twig',
            [
                'programs' => $programs,
                'form' => $form->createView(),
                'categories' => $this->categories,
                'search' => $isSearch,
            ]
        );
    }

    /**
     * @Route("/series/{slug<[a-zA-Z-]+[0-9]*>}", name="wild_show")
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
        $program = $programRepository->findOneBy(['slug' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException(
                'La série \'' . $slug . '\' n\'existe pas ou a été supprimé.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'seasons' => $program->getSeasons(),
            'categories' => $this->categories,
        ]);
    }

    /**
     * @Route("/series/category/{categoryName}",
     *      name="wild_category",
     *      requirements={"categoryName"="[A-Za-z-]+"},
     *     options={"utf8": true}
     * )
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
            'categories' => $this->categories,
            'category' => $category,
        ]);
    }

    /**
     * @Route("/series/{slug<[a-zA-Z-]+[0-9]*>}/{id<[0-9]+>}/season/{number<[0-9]+>}", name="wild_show_season")
     * @param int                $id
     * @param SeasonRepository   $seasonRepository
     * @param Request            $request
     * @param PaginatorInterface $paginator
     * @param CommentRepository  $commentRepository
     * @return Response
     */
    public function showBySeason(
        int $id,
        SeasonRepository $seasonRepository,
        Request $request,
        PaginatorInterface $paginator,
        CommentRepository $commentRepository
    ): Response {
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
            'categories' => $this->categories,
            'comment' => $commentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/series/{slug<[a-zA-Z-]+[0-9]*>}/{episode<[a-zA-Z-]+[0-9]*>}", name="wild_show_episode")
     * @ParamConverter("episode", options={"mapping": {"slug": "episode"}})
     * @param EpisodeRepository      $episodeRepository
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @param string                 $episode
     * @param Flash                  $flash
     * @return Response
     */
    public function showEpisode(
        EpisodeRepository $episodeRepository,
        Request $request,
        EntityManagerInterface $manager,
        $episode,
        Flash $flash
    ): Response {

        $episode = $episodeRepository->findOneBy(['slug' => $episode]);

        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $comment = new Comment();

            $comment->setAuthor($this->getUser());
            $comment->setComment($form['comment']->getData());
            $comment->setEpisode($episode);
            $comment->setPostedAt(new DateTime());
            $comment->setRate($form['rate']->getData());
            $manager->persist($comment);
            $manager->flush();

            $flash->createFlash('create');

            return $this->redirectToRoute('wild_show_episode', [
                'slug' => $episode->getSeason()->getProgram()->getSlug(),
                'episode' => $episode->getSlug(),
            ]);
        }

        return $this->render('wild/show/episode.html.twig',
            [
                'episode' => $episode,
                'season' => $episode->getSeason(),
                'program' => $episode->getSeason()->getProgram(),
                'categories' => $this->categories,
                'comments' => $episode->getComments(),
                'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/wild/actor/{slug<[a-zA-Z-]+>}", name="wild_actor")
     * @ParamConverter("actor", options={"mapping": {"slug": "slug"}})
     * @param ActorRepository $actorRepository
     * @param                 $slug
     * @return Response
     */
    public function showByActor(
        ActorRepository $actorRepository, $slug
    ): Response
    {
        $actor = $actorRepository->findOneBy(['slug' => $slug]);

        return $this->render('wild/showByActor.html.twig', [
            'actor' => $actor,
            'categories' => $this->categories,
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
                12
            ),
            'categories' => $this->categories,
        ]);
    }
}
