<?php
require "includes/db.php"; // Richiedere il file includes/db.php
require "includes/functions.php"; // Richiedere il file includes/functions.php
require('includes/FPDF-master/fpdf.php'); // Richiedere il file includes/FPDF-master/fpdf.php

// Importare i font forniti dalla libreria FPDF-master
const FPDF_FONTPATH = 'includes/FPDF-master/font';

/*
// Verificare che l'utente sia admin o user
$userGroupId = $_SESSION["group_id"];

// Assegnare i permessi che spettano a quella categoria di utente (admin o user)
$canAccessSettings = checkPermission($pdo, $userGroupId, "access_settings");*/
?>
<!DOCTYPE html>
<html lang = "it">
<head>
    <meta charset = "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <link rel = "stylesheet" href = "assets/css/bootstrap.min.css">
    <link rel = "stylesheet" href = "assets/css/leviws-2Stile.css">

    <style>
        .boxLoghi {
            position: absolute;
            bottom: 79.9%;
            width: 100%;
            height: 17.81%;
            background: rgba(0, 0, 0, 0.6)
        }
        .boxReport {
             position: absolute;
             bottom: 40%;
             width: 98.8%;
             height: 25%;
         }
        a:hover{
            text-decoration: underline; /* Aggiunge sottolineatura */
            transform: scale(1.2); /* Leggero ingrandimento */
        }
    </style>
    <script src = "assets/js/jquery-3.7.1.js"></script>
    <script src = "assets/js/bootstrap.bundle.min.js"></script>
    <script src = "assets/js/leviws-2Script.js"></script>
    <title>Compila relazione</title>
</head>
<body>
<div class = "navbar navbar-expand-lg navbar-dark bg-dark">
    <div class = "container">
        <a class = "navbar-brand" href = "https://www.istitutolevi.edu.it/" target = "_blank">IIS Primo Levi in <img src = "images/logo.gif" alt = "Logo">!</a>
        <div class = "collapse navbar-collapse">
            <ul class = "navbar-nav ms-auto">
                <li class = "nav-item"><a href = "home.php" target = "_blank" class = "nav-link">Home</a></li>
                <li class = "nav-item"><a href = "#" target = "_blank" class = "nav-link">Invia proposta</a></li>
                <li class = "nav-item"><a href = "#" target = "_blank" class = "nav-link">Compila modulo</a></li>
                <li class = "nav-item"><a href = "#" target = "_blank" class = "nav-link">Stampa modulo</a></li>
                <?php //if ($canAccessSettings): ?>
                    <li class = "nav-item"><a href = "gestione_utenti.php" target = "_blank" class = "nav-link">Gestione utenti</a></li>
                <?php //endif; ?>
                <li class = "nav-item"><a href = "#" target = "_blank" class = "nav-link">Gestione proposte</a></li>
                <li class = "nav-item"><a href = "invia_relazione.php" class = "nav-link">Compila relazione</a></li>
                <li class = "nav-item"><a href = "#" target = "_blank" class = "nav-link">Contatti</a></li>
                <li class = "nav-item"><a href = "logout.php" class = "nav-link">Log out</a></li>
            </ul>
        </div>
    </div>
</div>
<div class = "container">
    <div class = "boxSfondo">
        <img src = "images/sfondo.jpg" class = "trasparenza img-fluid" style = "height: 1350px" alt = "IIS Primo Levi"/>
        <div class = "boxLoghi">
            <table>
                <tr>
                    <td><img src = "images/logoLevi.png" class = "transizioneInizio img-fluid" alt = "Logo Levi"/></a></td>
                    <td><img src = "images/logoFutura.png" class = "transizioneInizio img-fluid" alt = "Logo Futura"/></a></td>
                    <td><img src = "images/logoVignola.png" class = "transizioneInizio img-fluid" alt = "Logo Vignola"/></a></td>
                </tr>
            </table>
        </div>
        <div class = "boxReport">
            <h1 class = "text-center text-light">Scarica Relazione</h1>
            <form action="scarica_relazione_pdf.php" method = "POST" class = "mx-auto bg-light border rounded p-3" style = "max-width: 532px;">
                <div class = "mb-3">
                    <label for = "nomeReferente" class = "form-label text-dark">Nome referente</label>
                    <input type = "text" id = "nomeReferente" name = "nomeReferente" class = "form-control" required>
                </div>
                <div class = "mb-3">
                    <label for = "classeRiferita" class = "form-label text-dark">Classe riferita</label>
                    <input type = "text" id = "classeRiferita" name = "classeRiferita" class = "form-control" required>
                </div>
                <div class = "mb-3">
                    <label for = "dataPartenza" class = "form-label text-dark">Data di partenza</label>
                    <input type = "date" id = "dataPartenza" name = "dataPartenza" class = "form-control" required>
                </div>
                <div class = "mb-3">
                    <label for = "dataArrivo" class = "form-label text-dark">Data di arrivo</label>
                    <input type = "date" id = "dataArrivo" name = "dataArrivo" class = "form-control" required>
                </div>
                <div class = "mb-3">
                    <label for = "dataCompilazione" class = "form-label text-dark">Data compilazione relazione</label>
                    <input type = "date" id = "dataArrivo" name = "dataCompilazione" class = "form-control" required>
                </div>
                <div class = "mb-3">
                    <label for = "descrizione" class = "form-label text-dark">Descrizione</label>
                    <textarea id = "descrizione" name = "descrizione" class = "form-control" style = "width: 498px; min-height: 200px; max-height: 200px" required></textarea>
                </div>
                <button type = "submit" class = "btn btn-primary w-100" onclick> Scarica PDF </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
