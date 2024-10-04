<?php

namespace App\Controller;

use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TransactionController extends AbstractController
{
    #[Route('/transaction', name: 'app_transaction_pdf')]
    public function index(PdfService $pdfService): Response
    {
        return new PdfResponse(
            $pdfService->generatePdf($this->getUser()),
            'file.pdf'
        );
    }
}
