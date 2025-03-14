<?php

// Inizio della sessione
session_start();

// Verificare che l'utente (admin o user) si sia prima loggato
if (isset($_SESSION["user_id"]))

    // Reindirizzare alla home se autenticato
    header("Location: home.php");
else

    // Reindirizzare alla pagina di login se non autenticato
    header("Location: login.php");