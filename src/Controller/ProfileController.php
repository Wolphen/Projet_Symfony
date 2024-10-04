<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/profile/{userName}', name: 'app_profile')]
    public function index(string $userName, Request $request, SluggerInterface $slugger): Response
    {

        $nom_user = $this->entityManager->getRepository(User::class)->findOneBy(['pseudo' => $userName]);

        if (!$nom_user) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirectToRoute('app_home_page');
        }

        $user = $this->getUser();

        if ($user === $nom_user) {
            $products = $nom_user->getProducts();
            $infoFavori = $this->entityManager->getRepository(Favoris::class)->count(['user' => $user->getId()]);

            // Traitement du formulaire d'upload de l'image de profil
            if ($request->isMethod('POST')) {
                $profileImage = $request->files->get('profile_image');

                if ($profileImage) {
                    $originalFilename = pathinfo($profileImage->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $profileImage->guessExtension();

                    try {
                        $profileImage->move(
                            $this->getParameter('profile_images_directory'),
                            $newFilename
                        );
                        $user->setProfileImage($newFilename);
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Image de profil mise à jour');
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                    }
                }
            }

            return $this->render('profile/index.html.twig', [
                'controller_name' => 'ProfileController',
                'user' => $user,
                'infoFavori' => $infoFavori,
                'nom_user' => $nom_user,
                'products' => $products
            ]);
        }

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'nom_user' => $nom_user,
            'user' => $user,
            'products' => $nom_user->getProducts()
        ]);
    }
    #[Route('/profile/upload/{id}', name: 'app_upload_profile_picture')]
    public function uploadProfilePicture(Request $request, User $user, SluggerInterface $slugger): Response
    {
        if ($this->getUser() !== $user) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette image de profil.');
            return $this->redirectToRoute('app_profile', ['userName' => $user->getPseudo()]);
        }

        $file = $request->files->get('profile_picture');

        if ($file) {
            // Types MIME acceptés
            $allowedMimeTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/jpg'];

            if (in_array($file->getMimeType(), $allowedMimeTypes)) {
                // Générer le nom de fichier basé sur l'ID de l'utilisateur
                $filename = $user->getId() . '.' . $file->guessExtension();

                // Déplacer le fichier vers le répertoire approprié
                try {
                    $file->move(
                        $this->getParameter('profile_pictures_directory'),
                        $filename
                    );

                    // Mettre à jour le chemin de l'image dans la base de données
                    $user->setProfilePicture($filename);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    $this->addFlash('success', 'Image uploadée avec succès.');
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            } else {
                $this->addFlash('error', 'Type de fichier non supporté. Veuillez uploader une image PNG, JPEG ou GIF.');
            }
        } else {
            $this->addFlash('error', 'Aucun fichier sélectionné.');
        }

        return $this->redirectToRoute('app_profile', ['userName' => $user->getPseudo()]);
    }
}
