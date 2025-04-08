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
    <title>Gestione Bozze</title>
    <link rel = "stylesheet" href = "assets/css/bootstrap.min.css">
    <link rel = "stylesheet" href = "assets/css/datatables.min.css">
    <style>
        body {
            background-image: url("images/sfondo.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed
        }
        #dt-search-0, #dt-search-1 {
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
            width: 85%;
            height: 33.48%
        }
        .boxGestioneBozze {
            position: absolute;
            bottom: 25%;
            width: 85%;
            height: 25%
        }
        .boxGestioneBozzeStorico {
            display: none;
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
            const table = $("#draftsTable").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "azioni_bozza.php?action=read", // Script PHP per ottenere i dati
                    "type": "POST"
                },
                "columns": [

                    {"data": "id"},
                    {"data": "nome"},
                    {"data": "descrizione"},
                    {"data": "valida"},
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
            const story = $("#draftsStoryTable").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "azioni_bozza_storico.php?action=read", // Script PHP per ottenere i dati
                    "type": "POST"
                },
                "columns": [

                    {"data": "rif"},
                    {"data": "docente"},
                    {"data": "ruolo"},
                    {"data": "data_creazione"},
                    {
                        "data": "rif",
                        render: function (data) {
                            return `
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

            // Aggiungi bozza
            $("#addDraft").on("click", function() {
                $("#draftForm")[0].reset();
                $("#draftId").val("");
                $("#draftModal").modal("show");
            });

            // Mostra storico
            $("#showStory").on("click", function() {
                $(".boxGestioneBozze").hide();
                story.ajax.reload();
                $(".boxGestioneBozzeStorico").show();
            });

            // Mostra bozze
            $("#goBack").on("click", function() {
                $(".boxGestioneBozzeStorico").hide();
                $(".boxGestioneBozze").show();
            });

            // Modifica bozza
            table.on("click", ".editUser", function() {
                const draftId = $(this).data("id");
                $.get("azioni_bozza.php?action=edit&id=" + draftId, function(data) {
                    const draft = JSON.parse(data);
                    $("#draftId").val(draft.id);
                    $("#nome").val(draft.nome);
                    $("#descrizione").val(draft.descrizione);
                    $("#ruolo").val(draft.ruolo);
                    if (draft.valida === "Sì")
                        $("#valida").prop("checked", true);
                    else
                        $("#valida").prop("checked", false);
                    $("#draftModal").modal("show");
                });
            });

            // Elimina bozza
            table.on("click", ".deleteUser", function() {
                const draftId = $(this).data("id");
                const draftNome = $(this).parents("tr").find("td:eq(1)").text();
                const valida = $(this).parents("tr").find("td:eq(3)").text();
                if (valida === "No") {
                    if (confirm("Sei sicuro di voler eliminare la bozza: " + draftNome + '?')) {
                        $.post("azioni_bozza.php?action=delete", { id: draftId }, function() {
                            table.ajax.reload();
                        });
                    }
                } else {
                    window.alert("Invalida prima la bozza!");
                }
            });

            // Elimina storico
            story.on("click", ".deleteUser", function() {
                const draftRif = $(this).data("id");
                const docente = $(this).parents("tr").find("td:eq(1)").text();
                if (confirm("Sei sicuro di voler eliminare lo storico di " + docente + " dal seguente viaggio?")) {
                    $.post("azioni_bozza_storico.php?action=delete", { rif: draftRif }, function() {
                        story.ajax.reload();
                    });
                }
            });

            // Salva bozza (Creazione o Modifica)
            $("#draftForm").on("submit", function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.post("azioni_bozza.php?action=save", formData, function() {
                    $("#draftModal").modal("hide");
                    table.ajax.reload();
                });
            });
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
        <a id = "nav-titolo" href = "https://www.istitutolevi.edu.it" style = "font-family: 'Rockwell', serif" title = "IIS Primo Levi">IIS Primo Levi in <img src = "images/logo.gif" class = "img-fluid" alt = "Logo">!</a>
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
    <div class = "boxGestioneBozze">
        <div class = "container mt-5 p-2 bg-dark border rounded border-dark text-light">
            <button id = "addDraft" class = "btn btn-primary mb-3"><img src = "images/aggiungi.png" class = "img-fluid" alt = "Aggiungi"/></button>
            <button id = "showStory" class = "btn btn-primary mb-3"><img src = "images/storico.png" class = "img-fluid" alt = "Storico"/></button>
            <table id = "draftsTable" class = "table bg-dark text-light">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrizione</th>
                    <th>Validità</th>
                    <th data-dt-order = "disable">Azioni</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrizione</th>
                    <th>Validità</th>
                    <th>Azioni</th>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Modale per Creazione/Modifica Utenti -->
        <div class = "modal fade" id = "draftModal" tabindex = "-1" aria-labelledby = "draftModalLabel" aria-hidden = "true">
            <div class = "modal-dialog">
                <div class = "modal-content bg-dark">
                    <form id = "draftForm">
                        <div class = "modal-header">
                            <h5 class = "modal-title text-light" id = "draftModalLabel">Gestisci Bozza</h5>
                            <button type = "button" class = "btn-close btn-close-white" data-bs-dismiss = "modal" aria-label = "Close"></button>
                        </div>
                        <div class = "modal-body">
                            <input type = "hidden" id = "draftId" name = "draftId">
                            <input type = "hidden" id = "userId" name = "userId" value = <?php echo $_SESSION["user_id"]?>>
                            <div class = "mb-3">
                                <label for = "nome" class = "form-label text-light">Nome</label>
                                <input type = "text" class = "form-control" id = "nome" name = "nome" required>
                            </div>
                            <div class = "mb-3">
                                <label for = "descrizione" class = "form-label text-light">Descrizione</label>
                                <textarea class = "form-control" style = "width: 466px; min-height: 300px; max-height: 300px" id = "descrizione" name = "descrizione" required></textarea>
                            </div>
                            <div class = "mb-3">
                                <label for = "ruolo" class = "form-label text-light">Ruolo</label>
                                <select id = "ruolo" name = "ruolo" class = "form-select">
                                    <option value = "Referente di Viaggio">Referente di Viaggio</option>
                                    <option value = "Accompagnatore">Accompagnatore</option>
                                </select>
                            </div>
                            <div class = "mb-3">
                                <label for = "valida" class = "form-label text-light">Valida</label>
                                <input type = "checkbox" class = "form-check-input" id = "valida" name = "valida" value = "Sì">
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
    <div class = "boxGestioneBozzeStorico">
        <div class = "container mt-5 p-2 bg-dark border rounded border-dark text-light">
            <button id = "goBack" class = "btn btn-primary mb-3"><img src = "images/indietro.png" class = "img-fluid" alt = "Indietro"/></button>
            <table id = "draftsStoryTable" class = "table bg-dark text-light">
                <thead>
                <tr>
                    <th>RIF</th>
                    <th>Docente</th>
                    <th>Ruolo</th>
                    <th>Data</th>
                    <th data-dt-order = "disable">Azioni</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>RIF</th>
                    <th>Docente</th>
                    <th>Ruolo</th>
                    <th>Data</th>
                    <th>Azioni</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</body>
</html>