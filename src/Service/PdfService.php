<?php

namespace App\Service;

use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PdfService
{
    private $pdf;
    private $twig;

    public function __construct(Pdf $pdf, Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->pdf = $pdf;
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    public function generatePdf($user)
    {
        $transactions = $this->entityManager->getRepository(Transaction::class)->findBy(['user' => $user->getId()]);
        $html = $this->twig->render('transaction/index.html.twig', array(
            'user' => $user,
            'transactions' => $transactions,
        ));
        return $this->pdf->getOutputFromHtml($html);

    }
}
