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

        $getUser = $this->getUser();

        $repositoryProduct = $this->entityManager->getRepository(Product::class);

        $products = $repositoryProduct->findAll();

        $testForm = $this->createForm(SearchBarType::class);

        $parameters = $request->query->all();
        if (!empty($parameters['search_bar']['name'])) {
            $resultatRecherche = $parameters['search_bar']['name'];

            $products = $repositoryProduct->createQueryBuilder('parameters')
                ->where('parameters.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $resultatRecherche . '%')
                ->getQuery()
                ->getResult();
        }


        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'testForm' => $testForm,
            'user' => $getUser,
            'products' => $products,
        ]);
    }
}
