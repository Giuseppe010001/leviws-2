<?php

// Inizio della sessione
session_start();

// Dichiarazione della variabile globale $pdo, necessaria per il file db.php
global $pdo;

// Verificare che l'utente (admin o user) si sia prima loggato
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require "includes/db.php"; // Richiedere il file includes/db.php
require 'includes/PHPMailer/PHPMailerAutoload.php'; // Richiedere il file includes/PHPMailer/PHPMailerAutoload.php

// Dichiarazione ed implementazione variabili di conferma ed errore nell'invio della e-mail
$sent = "";
$error = "";

// Dati ricevuti mediante metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Dati dal form
    // Destinatario
    $destinatario = $_POST["destinatario"];
    $destinatario = strtok($destinatario, " ");
    $nomeDestinatario = $destinatario;
    $destinatario = strtok(" ");
    $cognomeDestinatario = $destinatario;
    // Oggetto
    $oggetto = $_POST["oggetto"];
    // Corpo
    $corpo = $_POST["corpo"];

    // Mittente
    // Nome del mittente
    $stmt = $pdo->prepare("SELECT `nome` FROM `docente` WHERE docente.id = :id");
    $stmt -> bindValue(":id", $_SESSION["user_id"]);
    $stmt -> execute();
    $nomeMittente = $stmt->fetchColumn();
    // Email del mittente
    $stmt = $pdo->prepare("SELECT `email` FROM `docente` WHERE docente.id = :id");
    $stmt -> bindValue(":id", $_SESSION["user_id"]);
    $stmt -> execute();
    $emailMittente = $stmt->fetchColumn();

    // Destinatario
    // Nome del destinatario
    $stmt = $pdo->prepare("SELECT `nome` FROM `docente` WHERE `nome` = :nome AND `cognome` = :cognome");
    $stmt -> bindValue(":nome", $nomeDestinatario);
    $stmt -> bindValue(":cognome", $cognomeDestinatario);
    $stmt -> execute();
    $nomeDestinatario = $stmt->fetchColumn();
    // Email del destinatario
    $stmt = $pdo->prepare("SELECT `email` FROM `docente` WHERE `nome` = :nome AND `cognome` = :cognome");
    $stmt -> bindValue(":nome", $nomeDestinatario);
    $stmt -> bindValue(":cognome", $cognomeDestinatario);
    $stmt -> execute();
    $emailDestinatario = $stmt->fetchColumn();

    // Istanziare un oggetto PHPMailer
    $mail = new PHPMailer;

    // Configurazione server SMTP
    $mail -> isSMTP(); // Utilizzare server SMTP
    $mail -> Host = "smtp.gmail.com"; // Nome del server SMTP da utilizzare
    $mail -> SMTPAuth = true; // Abilitazione autenticazione server SMTP
    $mail -> Username = $emailMittente; // Indirizzo e-mail del mittente
    $mail -> Password = "Giuppy+1010110101010!!!"; // Password del mittente
    $mail -> SMTPSecure = "tls"; // Abilitazione crittografia protocollo TLS
    $mail -> Port = 587; // Connessione alla porta 587

    // Verifica di eventuali errori nella procedura di invio della e-mail
    try {
        $mail -> setFrom($emailMittente, $nomeMittente); // Mittente
        $mail -> addAddress($emailMittente, $nomeMittente); // Destinatario

        $mail -> Subject = $oggetto; // Oggetto
        $mail -> Body = $corpo; // Corpo

        // Verifica dell'invio della e-mail
        if ($mail -> send()) {
            $sent = "Messaggio inviato!";
        } else {
            $error = "Messaggio non inviato!";
        }
    } catch (phpmailerException $e) {
        $error = "Si è verificato un errore: ".$e->getMessage().'.';
    }
}
?>
<!DOCTYPE html>
<html lang = "it">
<head>
    <meta charset = "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Contatti</title>
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
        #nav-titolo {
            color: white;
            text-decoration: none
        }
        @media screen and (min-width: 1920px) and (max-width: 3840px) {
            #nav-titolo {
                font-size: 20px;
            }
            .nav-elemento {
                font-size: 18px;
            }
        }
        @media screen and (min-width: 1280px) and (max-width: 1920px) {
            #nav-titolo {
                font-size: 18px;
            }
            .nav-elemento {
                font-size: 16px;
            }
        }
        @media screen and (min-width: 640px) and (max-width: 1280px) {
            #nav-titolo {
                font-size: 14px;
            }
            .nav-elemento {
                font-size: 12px;
            }
        }
        @media screen and (min-width: 360px) and (max-width: 640px) {
            #nav-titolo {
                font-size: 10px;
            }
            .nav-elemento {
                font-size: 8px;
            }
        }
        #nav-titolo:hover, .nav-elemento:hover {
            color: white;
            background-color: black;
            transition-duration: 1s;
            transform: scale(1.1)
        }
        .transizioneInizio {
            display: none
        }
        .boxLoghi {
            position: absolute;
            bottom: 55%;
            width: 85%;
            height: 33.48%
        }
        .boxContatti {
            position: absolute;
            bottom: 10%;
            width: 85%;
            height: 25%
        }
    </style>
    <script src = "assets/js/jquery-3.7.1.js"></script>
    <script src = "assets/js/bootstrap.bundle.min.js"></script>
    <script src = "assets/js/leviws-2Script.js"></script>
    <script>

    </script>
</head>
<body>
<div class = "navbar navbar-expand-lg navbar-dark bg-dark">
    <div class = "container">
        <a id = "nav-titolo" href = "https://www.istitutolevi.edu.it" target = "_blank" title = "IIS Primo Levi">IIS Primo Levi in <img src = "images/logo.gif" class = "img-fluid" alt = "Logo">!</a>
        <div class = "collapse navbar-collapse">
            <ul class = "navbar-nav ms-auto">
                <li class = "nav-item"><a href = "home.php" class = "nav-link nav-elemento">Home</a></li>
                <li class = "nav-item"><a href = "invia_proposta.php" class = "nav-link nav-elemento">Invia proposta</a></li>
                <li class = "nav-item"><a href = "stampa_autorizzazione.php" class = "nav-link nav-elemento">Stampa autorizzazione</a></li>
                <?php if ($_SESSION["group_id"] == 1): ?>
                    <li class = "nav-item"><a href = "gestione_utenti.php" class = "nav-link nav-elemento">Gestione utenti</a></li>
                <?php endif; ?>
                <li class = "nav-item"><a href = "gestione_bozze.php" class = "nav-link nav-elemento">Gestione bozze</a></li>
                <li class = "nav-item"><a href = "invia_relazione.php" class = "nav-link nav-elemento">Compila relazione</a></li>
                <li class = "nav-item"><a href = "contatti.php" class = "nav-link nav-elemento">Contatti</a></li>
                <li class = "nav-item"><a href = "logout.php" class = "nav-link nav-elemento">Log out</a></li>
            </ul>
        </div>
    </div>
</div>
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
    <div class = "boxContatti">
        <h2 class = "text-center text-light">Contatti</h2>
        <form method = "POST" class = "mx-auto bg-light border rounded p-3" style = "max-width: 532px;">
            <?php if ($sent): ?>
                <div class = "alert alert-success"><?php echo $sent ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class = "alert alert-danger"><?php echo $error ?></div>
            <?php endif; ?>
            <div class = "mb-3">
                <label for = "destinatario" class = "form-label text-dark">Destinatario</label>
                <select id = "destinatario" name = "destinatario" class = "form-select">
                    <option value = "Mario Arcangelo Sorvillo">Mario Arcangelo Sorvillo</option>
                    <option value = "Emanuele Gnoni">Emanuele Gnoni</option>
                    <option value = "Giuseppe Carlino">Giuseppe Carlino</option>
                </select>
            </div>
            <div class = "mb-3">
                <label for = "oggetto" class = "form-label text-dark">Oggetto</label>
                <input type = "text" id = "oggetto" name = "oggetto" class = "form-control" required>
            </div>
            <div class = "mb-3">
                <label for = "corpo" class = "form-label text-dark">Corpo</label>
                <textarea id = "corpo" name = "corpo" class = "form-control" style = "width: 498px; min-height: 200px; max-height: 200px" required></textarea>
            </div>
            <button type = "submit" class = "btn btn-primary w-100">Invia</button>
        </form>
    </div>
</div>
</body>
</html>
