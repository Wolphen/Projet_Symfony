<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifyInfoProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ModifyInfoProfileController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private UserPasswordHasherInterface $passwordHasher) {}


    #[Route('/profile/modify/{id}', name: 'app_modify_info_profile')]
    public function index(User $id, Request $request): Response
    {
        $form = $this->createForm(ModifyInfoProfileType::class, $id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id->setPassword($this->passwordHasher->hashPassword($id, $form->get('password')->getData()));
            $this->entityManager->persist($id);
            $this->entityManager->flush();
            return $this->redirectToRoute("app_profile");
        }

        return $this->render('modify_info_profile/index.html.twig', [
            'form' => $form,
            'user' => $id,
        ]);
    }
}
