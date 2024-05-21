<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @Route("/")
 */
class MainController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    /**
     * @Route("/", name="app_index", methods={"GET"})
     */
    public function index(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        $email = (new Email())
            ->from('fsjd@oo.dd')
            ->to('test@test.com')
            ->subject('test@gmai')
            ->text("cghjkl");

        $this->mailer->send($email);
        return $this->render('main/index.html.twig', [
            'formations' => $formationRepository->findAll(),
            'categories' => $categoryRepository->findAll()
        ]);
    }

}