<?php

namespace App\Services;

use TCPDF;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class AttestationPdfService
{

    public function generate($data)
    {
        // Créer un nouveau PDF en format paysage (L)
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

        // Supprimer l'en-tête et le pied de page
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Définir les marges à 0
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        // Ajouter une page
        $pdf->AddPage();

        // Ajouter l'image de fond (A4 paysage: 297mm x 210mm)
        $imagePath = $data['backgroundAttestation'];
        $signaturePath = $data['signatureAttestation'];
        $pdf->Image($imagePath, 0, 0, 297, 210, '', '', '', false, 300);

        // Définir la police pour le corps du texte
        $pdf->SetFont('helvetica', '', 11);
        $pdf->SetTextColor(0, 0, 0);

        // Nom du médecin
        // Ajuster ces coordonnées en fonction de votre image
        $pdf->SetXY(80, 80);
        $pdf->Cell(140, 10, 'Je soussigné Pr. '.$data['presidentNom'].' Président de l’Ordre National des Pharmaciens de Guinée, atteste', 0, 1, 'C');

        $pdf->SetXY(80, 86);
        $pdf->Cell(140, 10, 'par la présente avoir inscrit au tableau de l’Ordre National des Pharmaciens de Guinée.', 0, 1, 'C');


        // Nom du médecin
        // Ajuster ces coordonnées en fonction de votre image
        $pdf->SetXY(80, 92);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(140, 10, 'Dr. '.$data['pharmacienNom']. ', '.$data['pharmacienProfil'].',', 0, 1, 'C');

        // Numéro d'enregistrement
        $pdf->SetXY(80, 98);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(140, 10, "sous le N° d'ordre" . $data['ordreNumero'], 0, 1, 'C');

        // Réinitialiser la police en normal (non gras) pour la mention
        $pdf->SetFont('helvetica', '', 11); // Notez le '' qui indique un style normal

        // Mention
        $pdf->SetXY(80, 120);
        $pdf->Cell(140, 10, 'En foi de quoi, je délivre la présente attestation pour servir et valoir ce que de droit.', 0, 1, 'C');

        // Date de validité
        $pdf->SetXY(100, 180);
        $pdf->Cell(100, 10, 'Attestation valable jusqu\'au ' . $data['validiteAttesation'], 0, 1, 'L');

        // Numéro de registre
        $pdf->SetXY(30, 180);
        $pdf->Cell(100, 10, $data['rngpsNumero'], 0, 1, 'L');

        // Signature du président
        $pdf->SetXY(220, 140);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(60, 10, 'Conakry, le ' . $data['dateOfValidation'], 0, 1, 'L');
        $pdf->SetXY(220, 145);
        $pdf->Cell(60, 10, 'Le Président', 0, 1, 'L');

        // Image example with resizing
        $pdf->Image($signaturePath, 230, 155, 25, 25, '', '', '', false, 300);


        // Date et lieu
        $pdf->SetXY(220, 180);
        $pdf->Cell(60, 10, 'Pr. '.$data['presidentNom'], 0, 1, 'L');

        // Générer le QR code
        $this->generateCustomQRCode(
            $pdf,
            $data['verifyAttestation_url'],
            140,   // Position X
            160,   // Position Y
            20,    // Taille globale (mm)
            [40, 81, 156],  // Couleur bleue (RGB)
            0.8,   // Module width
            0.8,   // Module height
             // Logo à superposer
        );


        return $pdf->Output('attestation.pdf', 'S');
    }

    protected function generateCustomQRCode(
        TCPDF $pdf,
        string $url,
        float $x,
        float $y,
        float $size,
        array $color,
        float $moduleWidth,
        float $moduleHeight,
        ?string $logoPath = null
    ): void {
        $style = [
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => $color,    // Couleur personnalisée
            'bgcolor' => [255,255,255], // Fond blanc
            'module_width' => $moduleWidth,
            'module_height' => $moduleHeight
        ];

        // Génération du QR Code
        $pdf->write2DBarcode($url, 'QRCODE,H', $x, $y, $size, $size, $style, 'N');

        // Superposition du logo si fourni
        if ($logoPath && file_exists($logoPath)) {
            $logoSize = $size * 0.3; // Logo fait 30% de la taille du QR
            $logoX = $x + ($size - $logoSize) / 2;
            $logoY = $y + ($size - $logoSize) / 2;

            $pdf->Image(
                $logoPath,
                $logoX,
                $logoY,
                $logoSize,
                $logoSize,
                '',
                '',
                '',
                false,
                300
            );
        }

    }

}


