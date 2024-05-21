<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ProfilePictureType;
use App\Repository\FormationRepository;
use App\Repository\InscriptionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


/**
     * @Route("/profile")
     */
class ProfileController extends AbstractController
{


    /**
     * @Route("/", name="app_profile")
     */
    public function index(Request $request, FormationRepository $formationRepository, InscriptionRepository $inscriptionRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

         $userId = $this->getUser()->getId();
         $user = $userRepository->find($userId);
         $form = $this->createForm(UserType::class, $user);
         $form->handleRequest($request);
 
         if ($form->isSubmitted() && $form->isValid()) {
             $entityManager->persist($user);
             $entityManager->flush();
            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
         }

         $form2 = $this->createForm(ProfilePictureType::class, $user);
         $form2->handleRequest($request);
 
         if ($form2->isSubmitted() && $form2->isValid()) {

            $picture = $form2->get('image')->getData();

            if ($picture) {

                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL

                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename.'-'.uniqid().'.'.$picture->guessExtension();



                // Move the file to the directory where brochures are stored

                try {

                    $picture->move(

                        $this->getParameter('profile_uploads_directory'),

                        $newFilename

                    );

                } catch (FileException $e) {

                    // ... handle exception if something happens during file upload

                }
                $user->setImage($newFilename);

            }

             $entityManager->persist($user);
             $entityManager->flush();
            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
         }
         
         $formations = [];
         $inscriptionsALL = [];
         $inscriptions = $inscriptionRepository->findByLearner($user);
         $learnersByFormation = [];


         if($this->isGranted("ROLE_INSTRUCTOR") ) {
         $formations = $formationRepository->findByInstructor($user);
         
        foreach ($formations as $formation) {
            $inscris = $inscriptionRepository->findBy(['formation' => $formation, 'validated' => true]);
            $learners = [];

            foreach ($inscris as $inscription) {
                $inscription->getLearner()->__load();
                $learner = $inscription->getLearner();
                $learners[] = $learner;
            }

            $learnersByFormation[$formation->getId()] = $learners;
        }
        } 


        return $this->render('profile/profile.html.twig', [
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'user' => $user,
            'inscriptions' => $inscriptions,
            'formationsInstructor' => $formations,
            'learners' => $learnersByFormation
        ]);
    }


}
