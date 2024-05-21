<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategoryRepository;
use App\Repository\CommentaireRepository;
use App\Repository\FormationRepository;
use App\Repository\InscriptionRepository;
use DateTime;
use Doctrine\ORM\Repository\Exception\InvalidFindByCall;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use getID3;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/formation")
 */

class FormationController extends AbstractController
{
    /**
     * @Route("/", name="app_formation_index", methods={"GET"})
     */
    public function index(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
            'categories' => $categoryRepository->findAll(),

        ]);
    }

    /**
     * @Route("/new", name="app_formation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FormationRepository $formationRepository, SluggerInterface $slugger): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                // dd($request);
                $logo = $form->get('thumbnail')->getData();

                if ($logo) {

                    $originalFilename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);

                    // this is needed to safely include the file name as part of the URL

                    $safeFilename = $slugger->slug($originalFilename);

                    $newFilename = $safeFilename.'-'.uniqid().'.'.$logo->guessExtension();



                    // Move the file to the directory where brochures are stored

                    try {

                        $logo->move(

                            $this->getParameter('uploads_directory'),

                            $newFilename

                        );

                    } catch (FileException $e) {

                        // ... handle exception if something happens during file upload

                    }
                    $formation->setThumbnail($newFilename);

                }

                $videos = $request->files->get('file');
                $duration = 0;

                foreach ($videos as $video) {

                    if ($video) {

                        $originalFilename = pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME);
    
                        // this is needed to safely include the file name as part of the URL
    
                        $safeFilename = $slugger->slug($originalFilename);
    
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$video->guessExtension();
                        
                        
    
                        // Move the file to the directory where brochures are stored
    
                        try {
    
                            $video->move(
    
                                $this->getParameter('uploads_directory'),
    
                                $newFilename
    
                            );
    
                        } catch (FileException $e) {
    
                            // ... handle exception if something happens during file upload
    
                        }

                        $vids [] = $newFilename;
                        $videoPath = 'project/public/formation/'.$newFilename;
                        $getID3 = new getID3;
                        $video_file = $getID3->analyze($videoPath);
                        $duration_seconds = $video_file;

                        // $duration += $duration_seconds;
                    }

                    $formation->setVideos($vids);
                    $formation->setDuration($duration);
                }

                $docs = $request->files->get('docs');

                for($i=0; $i<count($vids); $i++) {

                    if(!(isset($docs[$i]))) {
                        $docs[$i] = null;
                    }
                }


                foreach ($docs as $key=>$doc) {
                    
                    if ($doc) {

                        $originalFilename = pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME);
    
                        // this is needed to safely include the file name as part of the URL
    
                        $safeFilename = $slugger->slug($originalFilename);
    
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$doc->guessExtension();
                        
                        
    
                        // Move the file to the directory where brochures are stored
    
                        try {
    
                            $doc->move(
    
                                $this->getParameter('uploads_directory'),
    
                                $newFilename
    
                            );
    
                        } catch (FileException $e) {
    
                            // ... handle exception if something happens during file upload
    
                        }

                        $docs [$key] = $newFilename;
                        

                        // $duration += $duration_seconds;
                    }

                    // dd($docs);

                    $formation->setDocuments($docs);
                }

                // dd($request);
            $formation->setInstructor($this->getUser());
            if ($this->isGranted("ROLE_ADMIN")) {
                $formation->setValidated(1);
            }
            $formationRepository->add($formation, true);


            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
            'r' => $request
            // 'data' => json_decode($formation->getVideos())
        ]);
    }

    /**
     * @Route("/{id}", name="app_formation_show", methods={"GET"})
     */
    public function show(Request $request, Formation $formation,CommentaireRepository $commentaireRepository, InscriptionRepository $inscriptionRepository, SessionInterface $session): Response
    {    
        $inscription = [];
        if ($this->isGranted("ROLE_LEARNER"))   {
        $learner = $this->getUser();
        $inscription = $inscriptionRepository->findByFormation($formation, $learner);
    }

        $appartient = false;
        if ($this->isGranted("ROLE_INSTRUCTOR")) {
            $appartient = $formation->getInstructor() == $this->getUser();
        }

        
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
            'comments' => $commentaireRepository->findByFormation($formation),
            'now' => new DateTime(),
            'inscription' => $inscription,
            'appartient' => $appartient,
            'flash' => $session->getFlashBag()->all()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_formation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Formation $formation, FormationRepository $formationRepository): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formationRepository->add($formation, true);

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_formation_delete", methods={"POST"})
     */
    public function delete(Request $request, Formation $formation, FormationRepository $formationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $formationRepository->remove($formation, true);
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }

}
