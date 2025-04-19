<?php

namespace App\Services;

use TCPDF;
use Exception;

class ReceiptPdfService
{
    public function generate($data)
    {
        // Créer un nouveau PDF en format portrait (P)
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Supprimer l'en-tête et le pied de page
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Définir les marges
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);

        // Ajouter une page
        $pdf->AddPage();

        // Ajouter l'image de fond (A4 portrait: 210mm x 297mm)
        if (!empty($data['backgroundReceipt'])) {
            $this->addBackgroundImage($pdf, $data['backgroundReceipt']);
        }

        // Définir la police par défaut
        $pdf->SetFont('helvetica', '', 11);
        $pdf->SetTextColor(0, 0, 0);

        // Logo du marchand
        $this->addMerchantLogo($pdf, $data);

        // Sous-titre
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Reçu de paiement', 0, 1, 'L');
        $pdf->Ln(10);

        // Tableau "De" et "Détails"
        $this->addMerchantInfo($pdf, $data);

        // Section "Pour"
        $this->addCustomerInfo($pdf, $data);

        // Ligne de séparation
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());

        // Mention fiscale
        if (!empty($data['merchant_mention_fiscale'])) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 10, $data['merchant_mention_fiscale'], 0, 1, 'C');
        }

        // Numéro de page
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 10, 'Page 1 sur 1', 0, 1, 'C');

        return $pdf->Output('receipt.pdf', 'S');
    }

    protected function addBackgroundImage(TCPDF $pdf, string $imagePath): void
    {
        try {
            if (!file_exists($imagePath)) {
            throw new Exception("Background image not found");
        }

            $pdf->Image(
                $imagePath,
                0, 0,
                210, 297, // Dimensions A4 portrait
                '', '', '', false, 300
            );
        } catch (Exception $e) {
            error_log("Background image error: " . $e->getMessage());
        }
    }

    protected function addMerchantLogo(TCPDF $pdf, array $data): void
    {
        $pdf->SetFont('helvetica', 'B', 16);

        if (empty($data['logo'])) {
            $pdf->Cell(0, 10, $data['merchant_name'], 0, 1, 'L');
            $pdf->Ln(5);
            return;
        }

        try {
            // Vérification renforcée du chemin
            $logoPath = $data['logo'];

            // Solution 1: Vérification en 3 étapes
            if (!file_exists($logoPath)) {
                throw new Exception("Logo file does not exist at path: $logoPath");
            }

            if (!is_readable($logoPath)) {
                throw new Exception("Logo file exists but is not readable: $logoPath");
            }

            $imageInfo = @getimagesize($logoPath);
            if ($imageInfo === false) {
                throw new Exception("File is not a valid image: $logoPath");
            }

            // Solution 2: Utilisation de realpath + vérification
            $absolutePath = realpath($logoPath);
            if ($absolutePath === false) {
                throw new Exception("Cannot resolve absolute path for logo");
            }

            // Solution 3: Test d'ouverture du fichier
            if (!@fopen($absolutePath, 'r')) {
                throw new Exception("Could not open logo file for reading");
            }

            // Si toutes les vérifications passent
            $pdf->Image(
                $absolutePath,
                15, 15,
                40, 0, // largeur 40mm, hauteur proportionnelle
                '', '', '',
                false, 300, '',
                false, false, 0,
                false, false, false
            );
            $pdf->Ln(20);

        } catch (Exception $e) {
            // Journalisation détaillée
            error_log("LOGO ERROR: " . $e->getMessage());

            // Solution de repli
            $pdf->Cell(0, 10, $data['merchant_name'], 0, 1, 'L');
            $pdf->Ln(5);

            // Option: Ajouter un message de debug dans le PDF
            if (isset($data['debug'])) {
                $pdf->SetTextColor(255, 0, 0);
                $pdf->Cell(0, 5, "[Debug: Logo non chargé - " . basename($e->getMessage()) . "]", 0, 1);
                $pdf->SetTextColor(0, 0, 0);
            }
        }
    }

    protected function addMerchantInfo(TCPDF $pdf, array $data): void
    {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(90, 7, 'De', 0, 0, 'L');
        $pdf->Cell(90, 7, 'Détails', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 11);

        // Ligne 1
        $pdf->Cell(90, 5, $data['merchant_name'] ?? '', 0, 0, 'L');
        $pdf->Cell(90, 7, 'Référence: '.($data['payment_reference'] ?? ''), 0, 1, 'L');

        // Ligne 2
        $formattedAmount = isset($data['payment_amount'])
            ? number_format($data['payment_amount'], 0, '.', ' ').' GNF'
            : '0 GNF';

        $pdf->Cell(90, 7, $data['merchant_adresse'] ?? '', 0, 0, 'L');
        $pdf->Cell(90, 7, 'Montant payé : '.$formattedAmount, 0, 1, 'L');

        // Ligne 3
        $pdf->Cell(90, 7, $data['merchant_phone'] ?? '', 0, 0, 'L');
        $pdf->Cell(90, 7, 'Méthode de paiement : '.($data['payment_method'] ?? ''), 0, 1, 'L');

        // Ligne 4
        $pdf->Cell(90, 7, $data['merchant_email'] ?? '', 0, 0, 'L');
        $pdf->Cell(90, 7, 'Date de paiement : '.($data['payment_date'] ?? ''), 0, 1, 'L');



        $pdf->Ln(10);
    }

    protected function addCustomerInfo(TCPDF $pdf, array $data): void
    {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 7, 'Pour', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 7, $data['customer_nom'] ?? '', 0, 1, 'L');
        $pdf->Cell(0, 7, $data['customer_adresse'] ?? '', 0, 1, 'L');
        $pdf->Cell(0, 7, ($data['customer_ville'] ?? '').', '.($data['customer_pays'] ?? ''), 0, 1, 'L');
        $pdf->Cell(0, 7, $data['customer_telephone'] ?? '', 0, 1, 'L');
        $pdf->Cell(0, 7, $data['customer_email'] ?? '', 0, 1, 'L');

        $pdf->Ln(10);
    }
}
