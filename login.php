<?php

// Inizio della sessione
session_start();

// Dichiarazione della variabile globale $pdo, necessaria per i file db.php e functions.php
global $pdo;

// Verificare che l'utente (admin o user) si sia prima loggato
if (isset($_SESSION["user_id"])) {
    header("Location: home.php");
    exit;
}

require "includes/db.php"; // Richiedere il file includes/db.php
require "includes/functions.php"; // Richiedere il file includes/functions.php

// Variabile contenente la stringa di errore (inizialmente vuota) nel caso di errore nell'effettuazione del login
$error = "";

// Controllo della correttezza del login da parte dell'utente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (loginUser($pdo, $_POST["username"], $_POST["password"])) {
        header("Location: home.php");
        exit;
    } else {
        $error = "Username e/o password errati!!!";
    }
}
?>
<!DOCTYPE html>
<html lang = "it">
<head>
    <meta charset = "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel = "stylesheet" href = "assets/css/bootstrap.min.css">
    <style>
        body {
            background-image: url("images/sfondo.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed
        }
        td {
            padding-top: 50px;
            padding-left: 5px
        }
        .transizioneInizio {
            display: none
        }
        .boxLoghi {
            position: absolute;
            bottom: 70%;
            width: 85%;
            height: 33.48%
        }
        .boxLogin {
            position: absolute;
            bottom: 30%;
            width: 85%;
            height: 25%
        }
    </style>
    <script src = "assets/js/jquery-3.7.1.js"></script>
    <script src = "assets/js/bootstrap.bundle.min.js"></script>
    <script src = "assets/js/leviws-2Script.js"></script>
</head>
<body>
<div class = "container">
    <div class = "boxLoghi">
        <table>
            <tr>
                <td><img src = "images/logoLevi.png" class = "transizioneInizio img-thumbnail" alt = "Logo Levi"/></td>
                <td><img src = "images/logoFutura.png" class = "transizioneInizio img-thumbnail" alt = "Logo Futura"/></td>
                <td><img src = "images/logoVignola.png" class = "transizioneInizio img-thumbnail" alt = "Logo Vignola"/></td>
            </tr>
        </table>
    </div>
    <div class = "boxLogin">
        <h2 class = "text-center text-light">Login</h2>
        <form method = "POST" class = "mx-auto bg-light border rounded p-3" style = "max-width: 500px;">
            <?php if ($error): ?>
                <div class = "alert alert-danger"><?php echo $error ?></div>
            <?php endif; ?>
            <div class = "mb-3">
                <label for = "username" class = "form-label text-dark">Username</label>
                <input type = "text" id = "username" name = "username" class = "form-control" required>
            </div>
            <div class = "mb-3">
                <label for = "password" class = "form-label text-dark">Password</label>
                <input type = "password" id = "password" name = "password" class = "form-control" required>
            </div>
            <button type = "submit" class = "btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>