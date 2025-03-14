<?php

// Inizio della sessione
session_start();

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
        .boxGestioneUtenti {
            position: absolute;
            bottom: 10%;
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
        $(document).ready(function() {

            // Inizializza DataTables
            const table = $('#usersTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "azioni_utente.php?action=read", // Script PHP per ottenere i dati
                    "type": "POST"
                },
                "columns": [

                    {"data": "id"},
                    {"data": "username"},
                    {"data": "group_name"},
                    {
                        "data": "id",
                        render: function (data) {
                            return `
                            <button class = "btn btn-sm btn-warning editUser" data-id="${data}">Modifica</button>
                            <button class = "btn btn-sm btn-danger deleteUser" data-id="${data}">Elimina</button>
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
                $("#userForm")[0].reset();
                $("#userId").val("");
                $("#userModal").modal("show");
            });

            // Modifica utente
            table.on("click", ".editUser", function() {
                const userId = $(this).data("id");
                $.get("azioni_utente.php?action=edit&id=" + userId, function(data) {
                    const user = JSON.parse(data);
                    $("#userId").val(user.id);
                    $("#username").val(user.username);
                    $("#group").val(user.group_id);
                    $("#userModal").modal("show");
                });
            });

            // Elimina utente
            table.on("click", ".deleteUser", function() {
                const userId = $(this).data("id");
                const username = $(this).parents("tr").find("td:eq(1)").text();
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
    <div class = "boxGestioneUtenti">
        <h2 class = "text-center text-light">Gestione Bozze</h2>
        <div class = "container mt-5 p-2 bg-light border rounded">
            <button id = "addUser" class = "btn btn-primary mb-3">Aggiungi Bozza</button>
            <table id = "usersTable" class = "table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Gruppo</th>
                    <th data-dt-order = "disable">Azioni</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
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
                <div class = "modal-content">
                    <form id = "userForm">
                        <div class = "modal-header">
                            <h5 class = "modal-title" id = "userModalLabel">Gestisci Bozza</h5>
                            <button type = "button" class = "btn-close" data-bs-dismiss = "modal" aria-label = "Close"></button>
                        </div>
                        <div class = "modal-body">
                            <input type = "hidden" id = "userId" name = "userId">
                            <div class = "mb-3">
                                <label for = "username" class = "form-label">Username</label>
                                <input type = "text" class = "form-control" id = "username" name = "username" required>
                            </div>
                            <div class = "mb-3">
                                <label for = "password" class = "form-label">Password</label>
                                <input type = "password" class = "form-control" id = "password" name = "password">
                            </div>
                            <div class = "mb-3">
                                <label for = "group" class = "form-label">Gruppo</label>
                                <select id = "group" name = "group" class = "form-select">
                                    <option value = "1">Admin</option>
                                    <option value = "2">Utente</option>
                                </select>
                            </div>
                        </div>
                        <div class = "modal-footer">
                            <button type = "button" class = "btn btn-secondary" data-bs-dismiss = "modal">Chiudi</button>
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