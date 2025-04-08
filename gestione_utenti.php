<?php

// Inizio della sessione
session_start();

// Verificare che l'utente (admin o user) si sia prima loggato
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang = "it">
<head>
    <meta charset = "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Gestione Utenti</title>
    <link rel = "stylesheet" href = "assets/css/bootstrap.min.css">
    <link rel = "stylesheet" href = "assets/css/datatables.min.css">
    <style>
        body {
            background-image: url("images/sfondo.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed
        }
        #dt-search-0 {
            background-color: white;
            color: black
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
            width: 84.7%;
            height: 33.48%
        }
        .boxGestioneUtenti {
            position: absolute;
            bottom: 25%;
            width: 85%;
            height: 25%
        }
    </style>
    <script src = "assets/js/jquery-3.7.1.js"></script>
    <script src = "assets/js/bootstrap.bundle.min.js"></script>
    <script src = "assets/js/datatables.min.js"></script>
    <script src = "assets/js/dataTables.bootstrap5.js"></script>
    <script src = "assets/js/leviws-2Script.js"></script>
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
    <script>
        $(document).ready(function() {

            // Inizializza DataTables
            const table = $("#usersTable").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "azioni_utente.php?action=read", // Script PHP per ottenere i dati
                    "type": "POST"
                },
                "columns": [

                    {"data": "id"},
                    {"data": "docente"},
                    {"data": "username"},
                    {"data": "group_name"},
                    {
                        "data": "id",
                        render: function (data) {
                            return `
                            <button class = "btn btn-sm btn-primary editUser" data-id="${data}"><img src = "images/modifica.png" class = "img-fluid" alt = "Modifica"/></button>
                            <button class = "btn btn-sm btn-primary deleteUser" data-id="${data}"><img src = "images/elimina.png" class = "img-fluid" alt = "Elimina"/></button>
                        `;
                        }
                    }
                ],
                paging: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],  // Opzioni di paginazione
                language: {                   // Testo personalizzato per l'interfaccia
                    "url": "includes/it-IT.json"
                }
            });

            // Aggiungi utente
            $("#addUser").on("click", function() {
                $("#docenteBlocco").show();
                $("#userForm")[0].reset();
                $("#userId").val("");
                $("#password").prop("required", true);
                $("#userModal").modal("show");
            });

            // Modifica utente
            table.on("click", ".editUser", function() {
                const userId = $(this).data("id");
                $.get("azioni_utente.php?action=edit&id=" + userId, function(data) {
                    const user = JSON.parse(data);
                    $("#docenteBlocco").hide();
                    $("#userId").val(user.id);
                    $("#username").val(user.username);
                    $("#password").prop("required", false);
                    $("#userModal").modal("show");
                });
            });

            // Elimina utente
            table.on("click", ".deleteUser", function() {
                const userId = $(this).data("id");
                const username = $(this).parents("tr").find("td:eq(3)").text();
                if (confirm("Sei sicuro di voler eliminare l\'utente: " + username + '?')) {
                    $.post("azioni_utente.php?action=delete", { id: userId }, function() {
                        table.ajax.reload();
                    });
                }
            });

            // Salva utente (Creazione o Modifica)
            $("#userForm").on("submit", function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.post("azioni_utente.php?action=save", formData, function() {
                    $("#userModal").modal("hide");
                    table.ajax.reload();
                });
            });
        });
    </script>
</head>
<body>

<!-- Pulsante menu accanto alla GIF -->
<button class = "menu-toggle text-light">☰</button>

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
                <li class = "nav-item"><a href = "gestione_utenti.php" class = "nav-link nav-elemento text-light">Gestione utenti</a></li>
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
    <div class = "boxGestioneUtenti">
        <div class = "container mt-5 p-2 bg-dark border rounded border-dark text-light">
            <button id = "addUser" class = "btn btn-primary mb-3"><img src = "images/aggiungi.png" class = "img-fluid" alt = "Aggiungi"/></button>
            <table id = "usersTable" class = "table bg-dark text-light">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Docente</th>
                    <th>Username</th>
                    <th>Gruppo</th>
                    <th data-dt-order = "disable">Azioni</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Docente</th>
                    <th>Username</th>
                    <th>Gruppo</th>
                    <th>Azioni</th>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Modale per Creazione/Modifica Utenti -->
        <div class = "modal fade" id = "userModal" tabindex = "-1" aria-labelledby = "userModalLabel" aria-hidden = "true">
            <div class = "modal-dialog">
                <div class = "modal-content bg-dark">
                    <form id = "userForm">
                        <div class = "modal-header">
                            <h5 class = "modal-title text-light" id = "userModalLabel">Gestisci Utente</h5>
                            <button type = "button" class = "btn-close btn-close-white" data-bs-dismiss = "modal" aria-label = "Close"></button>
                        </div>
                        <div class = "modal-body">
                            <input type = "hidden" id = "userId" name = "userId">
                            <div id = "docenteBlocco" class = "mb-3">
                                <label for = "docente" class = "form-label text-light">Docente</label>
                                <input type = "text" class = "form-control" id = "docente" name = "docente" placeholder = "Nome Cognome" required>
                            </div>
                            <div class = "mb-3">
                                <label for = "username" class = "form-label text-light">Username</label>
                                <input type = "text" class = "form-control" id = "username" name = "username" required>
                            </div>
                            <div class = "mb-3">
                                <label for = "password" class = "form-label text-light">Password</label>
                                <input type = "password" class = "form-control" id = "password" name = "password">
                            </div>
                            <div class = "mb-3">
                                <label for = "group" class = "form-label text-light">Gruppo</label>
                                <select id = "group" name = "group" class = "form-select">
                                    <option value = '1'>Admin</option>
                                    <option value = '2'>Utente</option>
                                </select>
                            </div>
                        </div>
                        <div class = "modal-footer">
                            <button type = "button" class = "btn btn-light" data-bs-dismiss = "modal">Chiudi</button>
                            <button type = "submit" class = "btn btn-primary">Salva</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>