<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\SearchBarType;
use Symfony\Component\HttpFoundation\Request;

class HomePageController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/', name: 'app_home_page')]
    public function index(Request $request): Response
    {
        // Récupère l'utilisateur connecté ou null si personne n'est connecté
        $getUser = $this->getUser();

        $repositoryProduct = $this->entityManager->getRepository(Product::class);
        $products = $repositoryProduct->findAll();
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        $parameters = $request->query->all();
        if (isset($parameters['search_bar']) && !empty($parameters['search_bar']['Recherche'])) {
            $resultatRecherche = $parameters['search_bar']['Recherche'];
            $products = $repositoryProduct->createQueryBuilder('p')
                ->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $resultatRecherche . '%')
                ->getQuery()
                ->getResult();
        }

        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'user' => $getUser,
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{categoryName}', name: 'app_category_search')]
    public function category(string $categoryName): Response
    {
        $repositoryCategory = $this->entityManager->getRepository(Category::class);
        $category = $repositoryCategory->findOneBy(['name' => $categoryName]);

        if ($category) {
            $products = $category->getProducts();
        } else {
            $products = [];
        }

        return $this->render('category_search/index.html.twig', [
            'controller_name' => 'CategorySearchController',
            'products' => $products,
        ]);
    }
}
