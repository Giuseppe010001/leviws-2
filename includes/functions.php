<?php

// Funzione di accesso al portale
function loginUser($pdo, $username, $password): bool {
    $stmt = $pdo->prepare("SELECT * FROM utente WHERE username = :username");
    $stmt -> execute([":username" => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        session_start();
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["group_id"] = $user["rifGruppo"];
        return true;
    }
    return false;
}