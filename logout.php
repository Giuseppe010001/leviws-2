<?php

// Inizio della sessione
session_start();

// Distruggere tutti i dati della sessione
session_unset(); // Rimuovere tutte le variabili di sessione
session_destroy(); // Terminare la sessione

// Rimuovere eventuali cookie di sessione (opzionale, ma consigliato)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Reindirizzare alla pagina di login
header("Location: login.php");