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
?>
<!DOCTYPE html>
<html lang = "it">
<head>
    <meta charset = "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel = "stylesheet" href = "assets/css/bootstrap.min.css">
    <style>
        body {
            background-image: url("images/sfondo.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed
        }
        h1 {
            font-family: "Rockwell", serif;
            font-size: 60px
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
        @media screen and (max-width: 992px) {
            .navbar-nav {
                display: none; /* Nasconde i link della navbar */
            }

            .menu-toggle {
                display: block; /* Mostra il pulsante per aprire il menu laterale */
            }
        }
        @media screen and (min-width: 993px) {
            .sidebar {
                left: -250px !important; /* Nasconde il menu laterale quando la finestra è larga */
            }

            .overlay {
                display: none !important; /* Nasconde l'overlay */
            }
        }

        /* Nascondere completamente il men quando la navbar e' visibile */
        @media (min-width: 992px) {
            .menu-toggle, .sidebar, .overlay {
                display: none !important;
            }
        }

        #nav-titolo:hover, .nav-elemento:hover {
            color: white;
            background-color: black;
            text-decoration: underline;
            transition-duration: 0.3s;
        }

        /* MENU LATERALE */
        .sidebar {
            position: fixed;
            top: 0;
            left: -250px; /* Nasconde inizialmente il menu al di fuori dello schermo del dispositivo */
            width: 250px;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            transition: 0.3s;
            padding-top: 60px;
            z-index: 1000;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            font-size: 18px;
        }
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Bottone per aprire il menu (vicino alla GIF) */
        .menu-toggle {
            position: absolute;
            padding: 4px;
            top: 10px;
            left: 10px;
            font-size: 30px;
            cursor: pointer;
            background: none;
            border: none;
            color: white;
            z-index: 1100;
        }
        .menu-toggle:hover {
            color: white;
            background-color: black;
            transition-duration: 0.3s;
        }

        /* Sfondo scuro per chiudere il menu */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 900;
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
        .boxSaluto {
            position: absolute;
            bottom: 5%;
            width: 85%;
            height: 25%
        }
    </style>
    <script src = "assets/js/jquery-3.7.1.js"></script>
    <script src = "assets/js/bootstrap.bundle.min.js"></script>
    <script src = "assets/js/leviws-2Script.js"></script>
    <script type = "text/javascript">

        // Chiamata della funzione di gestione dell'effetto di intermittenza
        $(document).ready(intermittenza)

        // Funzione finalizzata ad implementare l'effetto di intermittenza nei widget h1
        function intermittenza() {
            $("h1").delay(1000).animate({opacity: 1}).delay(500).animate({opacity: 0.25}).delay(250).animate({opacity: 1});
        }
    </script>
    <script>
        $(document).ready(function () {
            function checkNavbar() {
                if ($(window).width() >= 992) {
                    $(".sidebar").hide(); // Nascondo inizialmente il menu
                    $(".overlay").hide();
                } else {
                    $(".sidebar").show();
                }
            }

            // Apri il menu al click quando si riduce la finestra
            $(".menu-toggle").click(function () {
                if ($(window).width() < 992) {
                    $(".sidebar").css("left", "0");
                    $(".overlay").fadeIn(); // jquery --> quando clicco compare il menu
                }
            });

            // Chiudi il menu se clicco al di fuori di esso
            $(".overlay").click(function () {
                $(".sidebar").css("left", "-250px");
                $(".overlay").fadeOut(); // jquery --> quando clicco da qualsiasi parte dello schermo (tranne il menu) viene nascosto il menu
            });

            // Controlla la larghezza della finestra quando cambia dimensione
            $(window).resize(checkNavbar);

            // Controlla subito all'apertura della pagina
            checkNavbar();
        });
    </script>
</head>
<body>

<!-- Pulsante menu accanto alla GIF -->
<button class = "menu-toggle">☰</button>

<!-- MENU LATERALE -->
<div class = "sidebar">
    <a href = "home.php" class = "nav-link text-light">Home</a>
    <a href = "compila_proposta.php" class = "nav-link text-light">Compila proposta</a>
    <a href = "stampa_autorizzazione.php" class = "nav-link text-light">Stampa autorizzazione</a>
    <a href = "gestione_utenti.php" class = "nav-link text-light">Gestione utenti</a>
    <a href = "gestione_bozze.php" class = "nav-link text-light">Gestione bozze</a>
    <a href = "invia_relazione.php" class = "nav-link text-light">Compila relazione</a>
    <a href = "contatti.php" class = "nav-link text-light">Contatti</a>
    <a href = "logout.php" class = "nav-link text-light">Log out</a>
</div>

<!-- Overlay per chiudere il menu -->
<div class = "overlay"></div>

<div class = "navbar navbar-expand-lg navbar-dark bg-dark">
    <div class = "container">
        <a id = "nav-titolo" href = "https://www.istitutolevi.edu.it" target = "_blank" style = "font-family: 'Rockwell', serif" title = "IIS Primo Levi">IIS Primo Levi in <img src = "images/logo.gif" class = "img-fluid" alt = "Logo">!</a>
        <div class = "collapse navbar-collapse">
            <ul class = "navbar-nav ms-auto">
                <li class = "nav-item"><a href = "home.php" class = "nav-link nav-elemento text-light">Home</a></li>
                <li class = "nav-item"><a href = "compila_proposta.php" class = "nav-link nav-elemento text-light">Compila proposta</a></li>
                <li class = "nav-item"><a href = "stampa_autorizzazione.php" class = "nav-link nav-elemento text-light">Stampa autorizzazione</a></li>
                <?php if ($_SESSION["group_id"] == 1): ?>
                    <li class = "nav-item"><a href = "gestione_utenti.php" class = "nav-link nav-elemento text-light">Gestione utenti</a></li>
                <?php endif; ?>
                <li class = "nav-item"><a href = "gestione_bozze.php" class = "nav-link nav-elemento text-light">Gestione bozze</a></li>
                <li class = "nav-item"><a href = "invia_relazione.php" class = "nav-link nav-elemento text-light">Compila relazione</a></li>
                <li class = "nav-item"><a href = "contatti.php" class = "nav-link nav-elemento text-light">Contatti</a></li>
                <li class = "nav-item"><a href = "logout.php" class = "nav-link nav-elemento text-light">Log out</a></li>
            </ul>
        </div>
    </div>
</div>
<div class = "container">
    <div class = "boxLoghi">
        <table>
            <tr>
                <td><a href = "https://www.istitutolevi.edu.it/" title = "IIS Primo Levi"><img src = "images/logoLevi.png" class = "transizioneInizio img-thumbnail" alt = "Logo Levi"/></a></td>
                <td><a href = "https://pnrr.istruzione.it/" title = "Futura"><img src = "images/logoFutura.png" class = "transizioneInizio img-thumbnail" alt = "Logo Futura"/></a></td>
                <td><a href = "https://www.comune.vignola.mo.it/" title = "Città di Vignola"><img src = "images/logoVignola.png" class = "transizioneInizio img-thumbnail" alt = "Logo Vignola"/></a></td>
            </tr>
        </table>
    </div>
    <div class = "boxSaluto">
        <?php

        // Selezione dello username dell'utente loggato
        $id = $_SESSION["user_id"];
        $stmt = $pdo->prepare("SELECT `username` FROM `utente` WHERE `id` = :id");
        $stmt -> execute([":id" => $id]);
        $user = $stmt->fetchColumn();

        // Saluto all'utente loggato secondo una modalità consona all'orario di accesso
        if (date('H') > 0 and date('H') < 12)
            echo "<h1 style = 'opacity: 0' class = 'text-center text-light'>Buongiorno $user!!!</h1>";
        elseif (date('H') > 12 and date('H') < 16)
            echo "<h1 style = 'opacity: 0' class = 'text-center text-light'>Buon pomeriggio $user!!!</h1>";
        else
            echo "<h1 style = 'opacity: 0' class = 'text-center text-light'>Buonasera $user!!!</h1>";
        ?>
    </div>
</div>
</body>
</html>