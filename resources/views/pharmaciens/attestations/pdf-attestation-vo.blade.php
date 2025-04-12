<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attestation Ordre National des Médécins de Guinée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            position: relative;
            background-image: url('{{public_path('/img/attestation-pharmacien.jpg')}}');
            background-size: contain;

        }

        .vertical-stripe {
            position: absolute;
            top: 0;
            left: 120px;
            bottom: 0;
            width: 60px;
            background-image: linear-gradient(to right,
            #CE1126 0px, #CE1126 20px,
            #FCD116 20px, #FCD116 40px,
            #009460 40px, #009460 60px);
            z-index: 0;
        }

        .emblem {
            position: absolute;
            top: 150px;
            left: 150px;
            transform: translateX(-50%);
            width: 160px;
            height: 160px;
            z-index: 1;
        }

        .header {
            text-align: center;
            font-weight: bold;
            margin-left: 100px;
            padding-top: 20px;
        }

        .content {
            margin-left: 120px;
            margin-right: 20px;
            padding: 20px;
            text-align: center;
        }

        .attestation-title {
            font-size: 50px;
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .attestation-text {
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .signature {
            text-align: right;
            margin-top: 50px;
            margin-right: 50px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 100px;
            margin-left: 120px;
            margin-right: 20px;
            font-weight: bold;
        }

        .divider {
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="vertical-stripe"></div>

<img src="{{asset('img/embleme-rpg.jpg')}}" class="emblem" alt="Emblème de la Guinée">

<div class="header">
    <div class="h3">RÉPUBLIQUE DE GUINÉE</div>
    <div class="h5">Travail - Justice - Solidarité</div>
    <div class="divider">------------------------</div>
    <div class="h4">MINISTÈRE DE LA SANTÉ ET DE L'HYGIÈNE PUBLIQUE</div>
    <div class="divider">------------------------</div>
    <div class="h2">ORDRE NATIONAL DES MÉDÉCINS DE GUINÉE</div>
</div>

<div class="content">
    <div class="attestation-title">Attestation</div>

    <div class="attestation-text">
        Je soussigné Pr. Hassane BAH Président de l'Ordre National des Pharmaciens de Guinée, atteste
        par la présente avoir inscrit au tableau de l'Ordre National des Pharmaciens de Guinée.<br>
        Dr. KABA FODE, Pharmacien Biologiste,<br>
        sous le N°<br><br>

        En foi de quoi, je délivre la présente attestation pour servir et valoir ce que de droit.
    </div>

    <div class="signature">
        <div>Conakry, le</div>
        <div>Le Président</div>
        <div style="margin-top: 50px;">Pr. Hassane BAH</div>
    </div>

    <div class="footer">
        <div>17777/A/2025</div>
        <div>Attestation valable jusqu'au</div>
    </div>
</div>
</body>
</html>
