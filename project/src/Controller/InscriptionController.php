<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Inscription;
use App\Form\InscriptionType;
use App\Repository\FormationRepository;
use App\Repository\InscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/inscription")
 */
class InscriptionController extends AbstractController
{


    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="app_inscription_index", methods={"GET"})
     */
    public function index(InscriptionRepository $inscriptionRepository): Response
    {
        return $this->render('inscription/index.html.twig', [
            'inscriptions' => $inscriptionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_inscription_new", methods={"GET", "POST"})
     */
    public function new(Request $request,$id,FormationRepository $formationRepository, InscriptionRepository $inscriptionRepository): Response
    {
        
        
        $learner = $this->getUser();
        $formation = $formationRepository->find($id);
        $existingInscription = $inscriptionRepository->findOneBy(['learner' => $learner, 'formation' => $formation]);

        if (! $existingInscription) {

        

            $inscription = new Inscription();
            $inscription->setFormation($formation);
            $inscription->setLearner($learner);
            $inscription->setValidated(false);
            $inscriptionRepository->add($inscription,true);
            $this->addFlash('success', 'Votre demande d\'inscription a été envoyée avec succés');

        }
        

        return $this->redirectToRoute('app_formation_show', ["id" => $id ], Response::HTTP_SEE_OTHER);


    }

    // /**
    //  * @Route("/{id}", name="app_inscription_show", methods={"GET"})
    //  */
    // public function show(Inscription $inscription): Response
    // {
    //     return $this->render('inscription/show.html.twig', [
    //         'inscription' => $inscription,
    //     ]);
    // }

    /**
     * @Route("/{id}/edit", name="app_inscription_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Inscription $inscription, InscriptionRepository $inscriptionRepository): Response
    {
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inscriptionRepository->add($inscription, true);

            return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_inscription_delete", methods={"POST"})
     */
    public function delete(Request $request, Inscription $inscription, InscriptionRepository $inscriptionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inscription->getId(), $request->request->get('_token'))) {
            $inscriptionRepository->remove($inscription, true);
        }

        return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
    }
}
