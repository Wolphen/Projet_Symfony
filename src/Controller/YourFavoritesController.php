<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Entity\Product;
use App\Entity\User;
use App\Service\NotificationsService;
use Doctrine\DBAL\Types\StringType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class YourFavoritesController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/remove_favorites/{id}', name: 'app_remove_favorite')]
    public function removeFavorite(Request $request, Product $id, Security $security)
    {
        $route = $request->query->get('route', 'app_home_page');

        $userLogged = $security->getUser();

        $repository = $this->entityManager->getRepository(Favoris::class);
        $favori = $repository->findOneBy(['product' => $id->getId(), 'user' => $userLogged]);

        $this->entityManager->remove($favori);
        $this->entityManager->flush();

        return $this->redirectToRoute($route);
    }

    #[Route('/add_favorites/{id}', name: 'app_add_favorite')]
    public function addFavorite(Request $request, Product $id, Security $security, NotificationsService $notificationsService)
    {
        $route = $request->query->get('route', 'app_home_page');

        $userLogged = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $security->getUser()->getId()]); ;



        $favori = new Favoris();
        $favori->setUser($userLogged);
        $favori->setProduct($id);
        $favori->setQuantity(1);

        $this->entityManager->persist($favori);
        $this->entityManager->flush();
        $notificationsService->sendNotification(
            'Favoris', $userLogged->getPseudo() .' a mis en favoris : '. $id->getName(),
            $id->getUser(), $id
        );

        return $this->redirectToRoute($route);
    }

    #[Route('/your_favorites', name: 'app_your_favorites')]
    public function index(Security $security): Response
    {
        $userLogged = $security->getUser();
        $repositoryUser = $this->entityManager->getRepository(User::class);
        $user = $repositoryUser->findOneBy(['id' => $userLogged]);

        $repositoryFavorites = $this->entityManager->getRepository(Favoris::class);
        $favorites = $repositoryFavorites->findBy(['user' => $user]);

        //dd($favorites);

        return $this->render('your_favorites/index.html.twig', [
            'controller_name' => 'YourFavoritesController',
            'favorites' => $favorites,
            'user' => $user,
        ]);
    }
}
