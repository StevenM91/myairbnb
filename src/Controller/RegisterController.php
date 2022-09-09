<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHash)
    {
        $this->manager = $manager;
        $this->passwordHash = $passwordHash;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(InscriptionType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setCreatedAt(new \DateTime);

            $passwordEncod = $this->passwordHash->hashPassword($user, $user->getPassword());
            $user->setPassword($passwordEncod);


            $this->manager->persist($user);
            $this->manager->flush();
        }


        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}