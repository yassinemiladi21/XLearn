<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\FormationRepository;
use App\Repository\InscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


 /**
     * @Route("/admin")
     */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="app_admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/users", name="app_admin_users")
     */

    public function users(UserRepository $userRepository): Response

    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/users/activate/{id}", name="app_admin_users_activate")
     */

     public function activateUser($id,UserRepository $userRepository): Response

     {
         $user = $userRepository->find($id);

         if ($user->isVerified()) {
            $user->setIsVerified(false);
            $userRepository->add($user,true);

         }else {
            $user->setIsVerified(true);
         $userRepository->add($user,true);

         }
         


         return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
     }

     /**
     * @Route("/users/delete/{id}", name="app_admin_users_delete")
     */

     public function deleteUser($id,Request $request, UserRepository $userRepository): Response

     {
        $user = $userRepository->find($id);

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/categories", name="app_admin_categories")
     */

     public function categories(CategoryRepository $categoryRepository): Response

     {
         return $this->render('admin/categories.html.twig', [
             'categories' => $categoryRepository->findAll(),
         ]);
     }

     /**
     * @Route("/categories/new", name="app_admin_categories_new")
     */

     public function newCategory(Request $request, CategoryRepository $categoryRepository) {

        $category = new Category();
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $category->setName($name);
            $category->setDescription($description);
            $categoryRepository->add($category,true);

            $this->addFlash(
                'success',
                'Catégorie ajoutée avec succés'
            );


             return $this->redirectToRoute('app_admin_categories', [], Response::HTTP_SEE_OTHER);
        }
     }


      /**
     * @Route("/formations", name="app_admin_formations")
     */

     public function formations(FormationRepository $formationRepository): Response

     {
         return $this->render('admin/formations.html.twig', [
             'formations' => $formationRepository->findAll(),
         ]);
     }

     
    /**
     * @Route("/formations/validate/{id}", name="app_admin_formations_validate")
     */

     public function validate ($id,FormationRepository $formationRepository): Response

     {
         $formation = $formationRepository->find($id);
         
        $formation->setValidated(1);
        $formationRepository->add($formation,true);


         return $this->redirectToRoute('app_admin_formations', [], Response::HTTP_SEE_OTHER);
     }

     /**
     * @Route("/formations/refuse/{id}", name="app_admin_formations_refuse")
     */

     public function refuse ($id,FormationRepository $formationRepository): Response

     {
         $formation = $formationRepository->find($id);
         
            $formation->setValidated(2);
            $formationRepository->add($formation,true);

         return $this->redirectToRoute('app_admin_formations', [], Response::HTTP_SEE_OTHER);
     }

      /**
     * @Route("/inscriptions", name="app_admin_inscriptions")
     */

     public function inscriptions(InscriptionRepository $inscriptionRepository): Response

     {
         return $this->render('admin/inscriptions.html.twig', [
             'inscriptions' => $inscriptionRepository->findAll(),
         ]);
     }

     
    /**
     * @Route("/inscriptions/validate/{id}", name="app_admin_inscriptions_validate")
     */

     public function validateInsc ($id,InscriptionRepository $inscriptionRepository): Response

     {
         $inscription = $inscriptionRepository->find($id);
         
        $inscription->setValidated(1);
        $inscriptionRepository->add($inscription,true);


         return $this->redirectToRoute('app_admin_inscriptions', [], Response::HTTP_SEE_OTHER);
     }

     /**
     * @Route("/inscriptions/refuse/{id}", name="app_admin_inscriptions_refuse")
     */

     public function refuseInsc ($id,InscriptionRepository $inscriptionRepository): Response

     {
         $inscription = $inscriptionRepository->find($id);
         
            $inscription->setValidated(2);
            $inscriptionRepository->add($inscription,true);

         return $this->redirectToRoute('app_admin_inscriptions', [], Response::HTTP_SEE_OTHER);
     }



     }




