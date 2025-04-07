<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement</title>
</head>
<body>
<h1>Reçu de Paiement - Ordre des Pharmaciens</h1>
<p><strong>Numéro d'inscription :</strong> {{ $numero_inscription }}</p>
<p><strong>Nom :</strong> {{ $prenom }} {{ $nom }}</p>
<p><strong>Montant payé :</strong> {{ $montant }} GNF</p>
<p><strong>Date du paiement :</strong> {{ $date_paiement }}</p>
<p><strong>Status :</strong> {{ $status }}</p>

<hr>
<p><strong>Merci de faire partie de l'Ordre des Pharmaciens de Guinée !</strong></p>
</body>
</html>
