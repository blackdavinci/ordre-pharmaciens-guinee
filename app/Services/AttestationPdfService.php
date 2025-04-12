<?php

namespace App\Services;

use TCPDF;

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
        $pdf->Cell(140, 10, 'sous le N° ' . $data['rngpsNumero'], 0, 1, 'C');

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
        $pdf->Cell(60, 10, 'Pr. Hassane BAH', 0, 1, 'L');


        return $pdf->Output('attestation.pdf', 'S');
    }
}
