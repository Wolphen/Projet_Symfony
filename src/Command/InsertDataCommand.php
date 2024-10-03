<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:insert_data',
    description: 'Insert data in database',
)]
class InsertDataCommand extends Command
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->caution('INSERTION DANS LA BASE DE DONNEE ! NE TOUCHER PLUS AU CLAVIER !');

        // Progress bars
        $progressBar = new ProgressBar($output, 2);
        $progressBar2 = new ProgressBar($output, 2);
        $progressBar3 = new ProgressBar($output, 2);

        // USER table insertion
        $io->section('TABLE USER');
        $progressBar->start();

        // Check if the user already exists
        $userRepository = $this->entityManager->getRepository(User::class);

        // User 1
        $existingUser1 = $userRepository->findOneBy(['email' => 'jean-marie.bigard@gmail.com']);
        if (!$existingUser1) {
            $user1 = new User();
            $user1->setPseudo('Jean-Marie bigard ma couilasse');
            $user1->setEmail('jean-marie.bigard@gmail.com');
            $user1->setPassword($this->passwordHasher->hashPassword($user1, 'JMB1234*'));
            $user1->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            $user1->setWallet(1000000000);
            $user1->setStars(5);
            $this->entityManager->persist($user1);
        }
        $progressBar->advance();

        // User 2
        $existingUser2 = $userRepository->findOneBy(['email' => 'jtaicasser@gmail.com']);
        if (!$existingUser2) {
            $user2 = new User();
            $user2->setPseudo('Brice de Nice');
            $user2->setEmail('jtaicasser@gmail.com');
            $user2->setPassword($this->passwordHasher->hashPassword($user2, 'Jtaicasser123*'));
            $user2->setRoles(['ROLE_USER']);
            $user2->setWallet(0);
            $user2->setStars(2);
            $this->entityManager->persist($user2);
        }
        $this->entityManager->flush();
        $progressBar->finish();

        // CATEGORY table insertion
        $io->section('TABLE CATEGORY');
        $progressBar2->start();

        $categoryRepository = $this->entityManager->getRepository(Category::class);

        // Category 1
        $existingCategory1 = $categoryRepository->findOneBy(['name' => 'Sportif']);
        if (!$existingCategory1) {
            $category1 = new Category();
            $category1->setName('Sportif');
            $this->entityManager->persist($category1);
        }
        $progressBar2->advance();

        // Category 2
        $existingCategory2 = $categoryRepository->findOneBy(['name' => 'Livre']);
        if (!$existingCategory2) {
            $category2 = new Category();
            $category2->setName('Livre');
            $this->entityManager->persist($category2);
        }
        $this->entityManager->flush();
        $progressBar2->finish();

        // PRODUCT table insertion
        $io->section('TABLE PRODUCT');
        $progressBar3->start();

        $productRepository = $this->entityManager->getRepository(Product::class);

        // Product 1
        $existingProduct1 = $productRepository->findOneBy(['name' => 'Planche de Surf']);
        if (!$existingProduct1) {
            $product1 = new Product();
            $product1->setName('Planche de Surf');
            $product1->setDescription('Une planche de surf vraiment incroyable à Nice');
            $product1->setImage('https://espace-deguisement.com/wp-content/uploads/2023/02/038348.jpg-r_1920_1080-f_jpg-q_x-xxyxx.jpg');
            $product1->setActif(true);
            $product1->setUser($user2 ?? $existingUser2);  // Assign user if already exists
            $product1->setQuantity(1);
            $product1->setTva(30);
            $product1->setCategory($category1 ?? $existingCategory1);  // Assign category if already exists
            $product1->setPrice(1000);
            $this->entityManager->persist($product1);
        }
        $progressBar3->advance();

        // Product 2 (example duplicate data, handled similarly)
        $existingProduct2 = $productRepository->findOneBy(['name' => 'Livre signé Jean-Marie Bigard']);
        if (!$existingProduct2) {
            $product2 = new Product();
            $product2->setName('Livre signé Jean-Marie Bigard');
            $product2->setDescription('Si vous adorer le travail de Jean-Marie Bigard ce livre est fait pour vous, état quasi neuf');
            $product2->setImage('https://static.fnac-static.com/multimedia/PE/Images/FR/NR/7d/2a/49/4795005/1540-1/tsp20240104082619/Les-lettres-de-Bigard.jpg');
            $product2->setActif(true);
            $product2->setUser($user2 ?? $existingUser2);
            $product2->setQuantity(1);
            $product2->setTva(30);
            $product2->setCategory($category1 ?? $existingCategory1);
            $product2->setPrice(1000);
            $this->entityManager->persist($product2);
        }
        $this->entityManager->flush();
        $progressBar3->finish();

        $output->writeln('USER: <info>ok</info>');
        $output->writeln('CATEGORY: <info>ok</info>');
        $output->writeln('PRODUCTS: <info>ok</info>');

        $io->success("FIN DE L'INSERTION !");
        return Command::SUCCESS;
    }
}