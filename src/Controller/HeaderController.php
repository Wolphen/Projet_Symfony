<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HeaderController extends AbstractController
{
    #[Route('/example', name: 'app_example')]
    public function index(): Response
    {
        // Définissez le contenu que vous voulez afficher dans l'en-tête
        $headerContent = 'Bienvenue sur notre site !';

        return $this->render('example/index.html.twig', [
            'headerContent' => $headerContent,
        ]);
    }
}
