<?php
require "includes/db.php"; // Richiedere il file includes/db.php

// Dichiarazione della variabile globale $pdo, necessaria per i file db.php e functions.php
global $pdo;

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
    <title>Compila proposta</title>
    <link rel = "stylesheet" href = "assets/css/bootstrap.min.css">
    <link rel = "stylesheet" href = "assets/css/datatables.min.css">
    <style>
        body {
            background-image: url("images/sfondo.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed
        }
        #dt-search-0, #dt-search-1, #dt-search-2, #dt-search-3 {
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

        /* Nascondere completamente il menu quando la navbar e' visibile */
        @media (min-width: 992px) {
            .menu-toggle, .sidebar, .overlay {
                display: none !important;
            }
        }

        #nav-titolo:hover, .nav-elemento:hover {
            color: white;
            background-color: black;
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
        .boxGestioneProposte {
            position: absolute;
            bottom: 25%;
            width: 85%;
            height: 25%
        }
        .boxGestioneProposteViaggi, .boxGestioneProposteClassi, .boxGestioneProposteStorico {
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
            const table = $("#proposalsTable").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "azioni_proposta.php?action=read", // Script PHP per ottenere i dati
                    "type": "POST"
                },
                "columns": [

                    {"data": "id"},
                    {"data": "descrizione"},
                    {
                        "data": "id",
                        render: function (data) {
                            return `
                            <button class = "btn btn-sm btn-primary editUser" data-id="${data}"><img src = "images/modifica.png" class = "img-fluid" alt = "Modifica"/></button>
                            <button class = "btn btn-sm btn-primary deleteUser" data-id="${data}"><img src = "images/scarica.png" class = "img-fluid" alt = "Scarica"/></button>
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
            const travel = $("#proposalsTravelTable").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "azioni_proposta_viaggi.php?action=read", // Script PHP per ottenere i dati
                    "type": "POST"
                },
                "columns": [

                    {"data": "rif"},
                    {"data": "nome"},
                    {"data": "tipo"},
                    {"data": "data_inizio"},
                    {"data": "data_fine"},
                    {"data": "mezzo"},
                    {"data": "destinazione"}
                ],
                paging: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],  // Opzioni di paginazione
                language: {                   // Testo personalizzato per l'interfaccia
                    "url": "includes/it-IT.json"
                }
            });
            const classi = $("#proposalsClassTable").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "azioni_proposta_classi.php?action=read", // Script PHP per ottenere i dati
                    "type": "POST"
                },
                "columns": [

                    {"data": "rif"},
                    {"data": "classe"},
                    {"data": "indirizzo"},
                    {"data": "coordinatore"},
                    {"data": "numerosita"},
                    {"data": "due_terzi"},
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
            const story = $("#proposalsStoryTable").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "azioni_proposta_storico.php?action=read", // Script PHP per ottenere i dati
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

            // Mostra Viaggi
            $("#showTravel").on("click", function() {
                $(".boxGestioneProposte").hide();
                travel.ajax.reload();
                $(".boxGestioneProposteViaggi").show();
            });

            // Mostra classi
            $("#showClass").on("click", function() {
                $(".boxGestioneProposte").hide();
                classi.ajax.reload();
                $(".boxGestioneProposteClassi").show();
            });

            // Mostra storico
            $("#showStory").on("click", function() {
                $(".boxGestioneProposte").hide();
                story.ajax.reload();
                $(".boxGestioneProposteStorico").show();
            });

            // Mostra proposte
            $("#goBackTravel").on("click", function() {
                $(".boxGestioneProposteViaggi").hide();
                $(".boxGestioneProposte").show();
            });
            $("#goBackClass").on("click", function() {
                $(".boxGestioneProposteClassi").hide();
                $(".boxGestioneProposte").show();
            });
            $("#goBackStory").on("click", function() {
                $(".boxGestioneProposteStorico").hide();
                $(".boxGestioneProposte").show();
            });

            // Modifica proposta
            table.on("click", ".editUser", function() {
                const proposalId = $(this).data("id");
                $.get("azioni_proposta.php?action=edit&id=" + proposalId, function(data) {
                    const proposal = JSON.parse(data);
                    $("#proposalId").val(proposalId);
                    $("#nome").val(proposal.nome);
                    $("#descrizione").val(proposal.descrizione);
                    $("#mezzo").val(proposal.mezzo);
                    $("#destinazione").val(proposal.destinazione);
                    $("#proposalModal").modal("show");
                });
            });

            // Elimina classe
            classi.on("click", ".deleteUser", function() {
                const proposalRif = $(this).data("id");
                const classe = $(this).parents("tr").find("td:eq(1)").text();
                if (confirm("Sei sicuro di voler eliminare la classe " + classe + " dal viaggio " + proposalRif + '?')) {
                    $.post("azioni_proposta_classi.php?action=delete", { rif: proposalRif }, function() {
                        classi.ajax.reload();
                    });
                }
            });

            // Elimina storico
            story.on("click", ".deleteUser", function() {
                const proposalRif = $(this).data("id");
                const docente = $(this).parents("tr").find("td:eq(1)").text();
                if (confirm("Sei sicuro di voler eliminare questo storico di " + docente + " dal viaggio " + proposalRif + '?')) {
                    $.post("azioni_proposta_storico.php?action=delete", { rif: proposalRif }, function() {
                        story.ajax.reload();
                    });
                }
            });

            // Salva proposta (Creazione o Modifica)
            $("#proposalForm").on("submit", function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.post("azioni_proposta.php?action=save", formData, function() {
                    $("#proposalModal").modal("hide");
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
        <a id = "nav-titolo" href = "https://www.istitutolevi.edu.it" style = "font-family: 'Rockwell', serif" title = "IIS Primo Levi">IIS Primo Levi in <img src = "images/logo.gif" class = "img-fluid" alt = "Logo"></a>
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
    <div class = "boxGestioneProposte">
        <div class = "container mt-5 p-2 bg-dark border rounded border-dark text-light">
            <button id = "showTravel" class = "btn btn-primary mb-3"><img src = "images/viaggi.png" class = "img-fluid" alt = "Viaggi"/></button>
            <button id = "showClass" class = "btn btn-primary mb-3"><img src = "images/classi.png" class = "img-fluid" alt = "Classi"/></button>
            <button id = "showStory" class = "btn btn-primary mb-3"><img src = "images/storico.png" class = "img-fluid" alt = "Storico"/></button>
            <table id = "proposalsTable" class = "table bg-dark text-light">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrizione</th>
                    <th data-dt-order = "disable">Azioni</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Descrizione</th>
                    <th>Azioni</th>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Modale per Creazione/Modifica Utenti -->
        <div class = "modal fade" id = "proposalModal" tabindex = "-1" aria-labelledby = "proposalModalLabel" aria-hidden = "true">
            <div class = "modal-dialog">
                <div class = "modal-content bg-dark">
                    <form id = "proposalForm">
                        <div class = "modal-header">
                            <h5 class = "modal-title text-light" id = "proposalModalLabel">Gestisci Proposta</h5>
                            <button type = "button" class = "btn-close btn-close-white" data-bs-dismiss = "modal" aria-label = "Close"></button>
                        </div>
                        <div class = "modal-body">
                            <fieldset>
                                <legend class = "text-light">Viaggio</legend>
                                <input type = "hidden" id = "proposalId" name = "proposalId">
                                <input type = "hidden" id = "userId" name = "userId" value = <?php echo $_SESSION["user_id"]?>>
                                <div class = "mb-3">
                                    <label for = "nome" class = "form-label text-light">Nome</label>
                                    <input type = "text" class = "form-control" id = "nome" name = "nome" required>
                                </div>
                                <div class = "mb-3">
                                    <label for = "tipo" class = "form-label text-light">Tipo</label>
                                    <select id = "tipo" name = "tipo" class = "form-select">
                                        <option value = '1'>Viaggio</option>
                                        <option value = '2'>Uscita</option>
                                    </select>
                                </div>
                                <div class = "mb-3">
                                    <label for = "descrizione" class = "form-label text-light">Descrizione</label>
                                    <textarea class = "form-control" style = "width: 466px; height: 300px" id = "descrizione" name = "descrizione" required></textarea>
                                </div>
                                <div class = "mb-3">
                                    <label for = "dataInizio" class = "form-label text-light">Data Inizio</label>
                                    <input type = "datetime-local" class = "form-control" id = "dataInizio" name = "dataInizio" required>
                                </div>
                                <div class = "mb-3">
                                    <label for = "dataFine" class = "form-label text-light">Data Fine</label>
                                    <input type = "datetime-local" class = "form-control" id = "dataFine" name = "dataFine" required>
                                </div>
                                <div class = "mb-3">
                                    <label for = "mezzo" class = "form-label text-light">Mezzo</label>
                                    <input type = "text" class = "form-control" id = "mezzo" name = "mezzo" required>
                                </div>
                                <div class = "mb-3">
                                    <label for = "destinazione" class = "form-label text-light">Destinazione</label>
                                    <input type = "text" class = "form-control" id = "destinazione" name = "destinazione" required>
                                </div>
                                <div class = "mb-3">
                                    <label for = "ruolo" class = "form-label text-light">Ruolo</label>
                                    <select id = "ruolo" name = "ruolo" class = "form-select">
                                        <option value = "Referente di Viaggio">Referente di Viaggio</option>
                                        <option value = "Accompagnatore">Accompagnatore</option>
                                    </select>
                                </div>
                            </fieldset>
                            <br>
                            <br>
                            <fieldset>
                                <legend class = "text-light">Classe</legend>
                                <div class = "mb-3">
                                    <label for = "numClasse" class = "form-label text-light">Classe</label>
                                    <select id = "numClasse" name = "numClasse" class = "form-select">
                                        <option value = '1'>1</option>
                                        <option value = '2'>2</option>
                                        <option value = '3'>3</option>
                                        <option value = '4'>4</option>
                                        <option value = '5'>5</option>
                                    </select>
                                </div>
                                <div class = "mb-3">
                                    <label for = "sezClasse" class = "form-label text-light">Sezione</label>
                                    <select id = "sezClasse" name = "sezClasse" class = "form-select">
                                        <option value = 'A'>A</option>
                                        <option value = 'B'>B</option>
                                        <option value = 'C'>C</option>
                                        <option value = 'D'>D</option>
                                        <option value = 'E'>E</option>
                                        <option value = 'F'>F</option>
                                        <option value = 'G'>G</option>
                                        <option value = 'H'>H</option>
                                        <option value = 'L'>L</option>
                                        <option value = 'M'>M</option>
                                        <option value = 'P'>P</option>
                                        <option value = 'R'>R</option>
                                        <option value = 'S'>S</option>
                                    </select>
                                </div>
                                <div class = "mb-3">
                                    <label for = "indirizzo" class = "form-label text-light">Indirizzo</label>
                                    <select id = "indirizzo" name = "indirizzo" class = "form-select">
                                        <option value = '1'>LSSA</option>
                                        <option value = '2'>ITT</option>
                                        <option value = '3'>IPIA</option>
                                        <option value = '4'>IPSC</option>
                                    </select>
                                </div>
                                <div class = "mb-3">
                                    <label for = "coordinatore" class = "form-label text-light">Coordinatore</label>
                                    <select id = "coordinatore" name = "coordinatore" class = "form-select">
                                        <?php

                                        $stmt = $pdo->prepare("SELECT `id`, `nome` FROM `docente`");
                                        $stmt -> execute();
                                        $docenti = $stmt->fetchALL();

                                        foreach ($docenti as $d) {
                                            $id = $d["id"];
                                            $nome = $d["nome"];
                                            echo "<option value = $id>$nome</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class = "mb-3">
                                    <label for = "numerosita" class = "form-label text-light">Numerosità</label>
                                    <input type = "number" class = "form-control" id = "numerosita" name = "numerosita" required>
                                </div>
                            </fieldset>
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
    <div class = "boxGestioneProposteViaggi">
        <div class = "container mt-5 p-2 bg-dark border rounded border-dark text-light">
            <button id = "goBackTravel" class = "btn btn-primary mb-3"><img src = "images/indietro.png" class = "img-fluid" alt = "Indietro"/></button>
            <table id = "proposalsTravelTable" class = "table bg-dark text-light">
                <thead>
                <tr>
                    <th>RIF</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Data inizio</th>
                    <th>Data Fine</th>
                    <th>Mezzo</th>
                    <th>Destinazione</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>RIF</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Data inizio</th>
                    <th>Data Fine</th>
                    <th>Mezzo</th>
                    <th>Destinazione</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class = "boxGestioneProposteClassi">
        <div class = "container mt-5 p-2 bg-dark border rounded border-dark text-light">
            <button id = "goBackClass" class = "btn btn-primary mb-3"><img src = "images/indietro.png" class = "img-fluid" alt = "Indietro"/></button>
            <table id = "proposalsClassTable" class = "table bg-dark text-light">
                <thead>
                <tr>
                    <th>RIF</th>
                    <th>Classe</th>
                    <th>Indirizzo</th>
                    <th>Coordinatore</th>
                    <th>Numerosità</th>
                    <th>2/3</th>
                    <th data-dt-order = "disable">Azioni</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>RIF</th>
                    <th>Classe</th>
                    <th>Indirizzo</th>
                    <th>Coordinatore</th>
                    <th>Numerosità</th>
                    <th>2/3</th>
                    <th>Azioni</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class = "boxGestioneProposteStorico">
        <div class = "container mt-5 p-2 bg-dark border rounded border-dark text-light">
            <button id = "goBackStory" class = "btn btn-primary mb-3"><img src = "images/indietro.png" class = "img-fluid" alt = "Indietro"/></button>
            <table id = "proposalsStoryTable" class = "table bg-dark text-light">
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