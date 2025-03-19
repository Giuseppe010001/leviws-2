<?php
require "includes/db.php"; // Richiedere il file includes/db.php

// Dichiarazione della variabile globale $pdo, necessaria per il file db.php
global $pdo;

// Prelievo del valore di action da gestione_utenti.php mediante il metodo GET. Nel caso action fosse vuoto, assegnare una stringa vuota
$action = $_GET["action"] ?? "";

// Lettura dal JSON
if ($action == "read") {

    // Parametri inviati da DataTables
    $start = isset($_POST["start"]) ? (int)$_POST["start"] : 0;
    $length = isset($_POST["length"]) ? (int)$_POST["length"] : 10;
    $searchValue = isset($_POST["search"]["value"]) ? trim($_POST["search"]["value"]) : "";
    $orderColumnIndex = isset($_POST["order"][0]["column"]) ? (int)$_POST["order"][0]["column"] : 0;
    $orderDirection = isset($_POST["order"][0]["dir"]) && in_array($_POST["order"][0]["dir"], ["asc", "desc"]) ? $_POST["order"][0]["dir"] : "asc";

    // Array di mappatura colonne (per ordinamento)
    $columns = ["id", "nome", "cognome", "username", "group_name"];

    // Configurazione iniziale della tabella di gestione utenti
    if (!empty($searchValue)) {
        if (!preg_match("/^[a-zA-Z0-9_ ]*$/", $searchValue)) {
            echo json_encode([
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
            exit;
        }
    }

    // Costruzione query principale
    $query = "SELECT u.id, u.username, g.nome as group_name, d.nome, d.cognome FROM `gruppo` g LEFT JOIN (`utente` u LEFT JOIN `docente` d ON u.id = d.rifUtente) ON g.id = u.rifGruppo";

    // Aggiunta filtro di ricerca
    if (!empty($searchValue))
        $query .= " WHERE u.username LIKE :search OR g.nome LIKE :search";

    // Aggiunta ordinamento
    $query .= " ORDER BY " . $columns[$orderColumnIndex] . " $orderDirection";

    // Aggiunta paginazione
    $query .= " LIMIT :start, :length";

    // Preparazione della query
    $stmt = $pdo->prepare($query);

    // Bind dei parametri
    if (!empty($searchValue))
        $stmt -> bindValue(":search", $searchValue);
    $stmt -> bindValue(":start", $start, PDO::PARAM_INT);
    $stmt -> bindValue(":length", $length, PDO::PARAM_INT);

    // Esecuzione della query
    $stmt -> execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Conteggio totale dei record
    $totalRecordsQuery = "SELECT COUNT(*) FROM `utente`";
    $totalRecords = $pdo->query($totalRecordsQuery)->fetchColumn();

    // Conteggio totale con filtro
    if (!empty($searchValue)) {
        $filteredRecordsQuery = "SELECT COUNT(*) FROM `utente` WHERE `username` LIKE :search";
        $stmtFiltered = $pdo->prepare($filteredRecordsQuery);
        $stmtFiltered -> bindValue(":search", $searchValue);
        $stmtFiltered -> execute();
        $filteredRecords = $stmtFiltered->fetchColumn();
    } else {
        $filteredRecords = $totalRecords;
    }

    // Restituzione del JSON
    echo json_encode([
        "recordsTotal" => $totalRecords,        // Totale record senza filtro
        "recordsFiltered" => $filteredRecords,  // Totale record filtrati
        "data" => $data                         // Dati della pagina corrente
    ]);

// Modifica utente
} elseif ($action == "edit") {
    $id = $_GET["id"];
    $stmt = $pdo->prepare("SELECT * FROM `utente` LEFT JOIN `docente` ON utente.id = docente.rifUtente WHERE utente.id = :rif");
    $stmt -> bindValue(":rif", $id, PDO::PARAM_INT);
    $stmt -> execute();
    echo json_encode($stmt -> fetch());

// Salvataggio utente
} elseif ($action == "save") {
    $id = $_POST["userId"] ?? null;
    $nome = $_POST["nome"];
    $cognome = $_POST["cognome"];
    $username = $_POST["username"];
    $password = $_POST["password"] ?? null;
    $group = $_POST["group"];

    if ($id) {

        // Aggiornamento utente
        $stmt = $pdo->prepare("UPDATE `utente` SET `username` = :username, `rifGruppo` = :group WHERE `id` = :id");
        $stmt -> bindValue(":username", $username);
        $stmt -> bindValue(":group", $group);
        $stmt -> bindValue(":id", $id, PDO::PARAM_INT);
        $stmt -> execute();
        $stmt = $pdo->prepare("UPDATE `docente` SET `nome` = :nome, `cognome` = :cognome, `rifUtente` = :rif WHERE `id` = :rif");
        $stmt -> bindValue(":nome", $nome);
        $stmt -> bindValue(":cognome", $cognome);
        $stmt -> bindValue(":rif", $id, PDO::PARAM_INT);
        $stmt -> execute();

        if ($password) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE `utente` SET `password` = :password_hash WHERE `id` = :id");
            $stmt -> bindValue(":password_hash", $password_hash);
            $stmt -> bindValue(":id", $id, PDO::PARAM_INT);
            $stmt -> execute();
        }
    } else {

        // Creazione utente
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO `utente` (`username`, `password`, `rifGruppo`) VALUES (:username, :password_hash, :group)");
        $stmt -> bindValue(":username", $username);
        $stmt -> bindValue(":password_hash", $password_hash);
        $stmt -> bindValue(":group", $group, PDO::PARAM_INT);
        $stmt -> execute();
        $stmt = $pdo->prepare("INSERT INTO `docente` (`nome`, `cognome`, `email`, `rifUtente`) VALUES (:nome, :cognome, :email, :rifUtente)");
        $stmt -> bindValue(":nome", $nome);
        $stmt -> bindValue(":cognome", $cognome);
        $stmt -> bindValue(":email", $cognome.'.'.$nome."@istitutolevi.edu.it");
        $stmt -> bindValue(":rifUtente", $id, PDO::PARAM_INT);
        $stmt -> execute();
    }

// Eliminazione utente
} elseif ($action == "delete") {
    $id = $_POST["id"];
    $stmt = $pdo->prepare("DELETE FROM `utente` WHERE `id` = :id");
    $stmt -> bindValue(":id", $id, PDO::PARAM_INT);
    $stmt -> execute();
    $stmt = $pdo->prepare("DELETE FROM `docente` WHERE `rifUtente` = :rif");
    $stmt -> bindValue(":rif", $id, PDO::PARAM_INT);
    $stmt -> execute();
}