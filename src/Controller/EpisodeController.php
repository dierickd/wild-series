<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Form\ProgramSearchType;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/episode")
 */
class EpisodeController extends AbstractController
{
    /**
     * @Route("/", name="episode_index", methods={"GET"})
     * @param EpisodeRepository  $episodeRepository
     * @param ProgramRepository  $programRepository
     * @param PaginatorInterface $paginator
     * @param Request            $request
     * @return Response
     */
    public function index(
        EpisodeRepository $episodeRepository,
        ProgramRepository $programRepository,
        PaginatorInterface $paginator,
        Request $request
    )
    {
        return $this->render('admin/episode/index.html.twig', [
            'episodes' => $paginator->paginate(
                $episodeRepository->findAll(),
                $request->query->getInt('page', 1),
                10
            ),
            'programs' => $programRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="episode_new", methods={"GET","POST"})
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param Slugify                $slugify
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager, Slugify $slugify): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager();

            $episode->setSlug($slugify->generate($episode->getTitle()));
            $entityManager->persist($episode);
            $entityManager->flush();

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('admin/episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="episode_show", methods={"GET"})
     * @param Episode $episode
     * @return Response
     */
    public function show(Episode $episode): Response
    {
        return $this->render('admin/episode/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="episode_edit", methods={"GET","POST"})
     * @param Request                $request
     * @param Episode                $episode
     * @param Slugify                $slugify
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, Episode $episode, Slugify $slugify, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager();

            $episode->setSlug($slugify->generate($episode->getTitle()));
            $entityManager->persist($episode);
            $entityManager->flush();

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('admin/episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="episode_delete", methods={"DELETE"})
     * @param Request $request
     * @param Episode $episode
     * @return Response
     */
    public function delete(Request $request, Episode $episode): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($episode);
            $entityManager->flush();
        }

        return $this->redirectToRoute('episode_index');
    }
}
