<?php

namespace App\Controller;

use DateTime;
use DateTimeImmutable;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\FormationRepository;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/commentaire")
 */
class CommentaireController extends AbstractController
{
    /**
     * @Route("/", name="app_commentaire_index", methods={"GET"})
     */
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="app_commentaire_new", methods={"GET", "POST"})
     */
    public function new($id,Request $request, CommentaireRepository $commentaireRepository, FormationRepository $formationRepository): Response
    {
        $commentaire = new Commentaire();
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            $commentaire->setCommentor($this->getUser());
            $commentaire->setContent($content);
            $commentaire->setCreatedAt(new DateTimeImmutable());
            $commentaire->setUpdatedAt(new DateTimeImmutable());
            $formation = $formationRepository->find($id);
            dump($formation);

            $commentaire->setFormation($formation);
            
            $commentaireRepository->add($commentaire,true);
       

            return $this->redirectToRoute('app_formation_show', ['id' => $formation->getId()]);
        }

        return $this->renderForm('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    /**
     * @Route("/{id}", name="app_commentaire_show", methods={"GET"})
     */
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_commentaire_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Commentaire $commentaire, CommentaireRepository $commentaireRepository): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaireRepository->add($commentaire, true);

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_commentaire_delete", methods={"POST"})
     */
    public function delete(Request $request, Commentaire $commentaire, CommentaireRepository $commentaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $commentaireRepository->remove($commentaire, true);
        }

        return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
