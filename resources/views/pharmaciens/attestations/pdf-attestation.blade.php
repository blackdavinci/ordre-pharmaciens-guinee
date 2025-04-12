<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Attestation Ordre National des Médécins de Guinée</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            /* Ajoutez du padding ou des marges selon vos besoins */
        }

        /* Styles pour le contenu à superposer */
        .doctor-name {
            position: absolute;
            top: 415px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 16px;
            font-weight: bold;
        }

        .registration-number {
            position: absolute;
            top: 450px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 16px;
        }

        .date {
            position: absolute;
            top: 520px;
            right: 150px;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Image en arrière-plan comme élément HTML -->
    <img src="{{ public_path('/img/attestation-pharmacien.jpg') }}" class="background-image" alt="Attestation Template">
</div>
</body>
</html>
