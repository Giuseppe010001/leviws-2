<?php
require "includes/db.php"; // Richiedere il file includes/db.php

// Dichiarazione della variabile globale $pdo, necessaria per i file db.php e functions.php
global $pdo;

// Prelievo del valore di action da gestione_bozze.php mediante il metodo GET. Nel caso action fosse vuoto, assegnare una stringa vuota
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
    $columns = ["rif", "docente", "ruolo", "data_creazione"];

    // Configurazione iniziale della tabella di gestione bozze
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
    $query = "SELECT b.id as rif, d.nome as docente, e.ruolo, e.data as data_creazione FROM `bozza` b JOIN (`effettua` e JOIN `docente` d ON e.rifDocente = d.id) ON e.rifBozza = b.id";

    // Aggiunta filtro di ricerca
    if (!empty($searchValue))
        $query .= " WHERE b.id LIKE :search OR d.nome LIKE :search OR e.ruolo LIKE :search";

    // Aggiunta ordinamento
    $query .= " ORDER BY " . $columns[$orderColumnIndex] . " $orderDirection";

    // Aggiunta paginazione
    $query .= " LIMIT :start, :length";

    // Preparazione della query
    $stmt = $pdo->prepare($query);

    // Bind dei parametri
    if (!empty($searchValue))
        $stmt->bindValue(":search", $searchValue);
    $stmt->bindValue(":start", $start, PDO::PARAM_INT);
    $stmt->bindValue(":length", $length, PDO::PARAM_INT);

    // Esecuzione della query
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Conteggio totale dei record
    $totalRecordsQuery = "SELECT COUNT(*) FROM `effettua`";
    $totalRecords = $pdo->query($totalRecordsQuery)->fetchColumn();

    // Conteggio totale con filtro
    if (!empty($searchValue)) {
        $filteredRecordsQuery = "SELECT COUNT(*) FROM `bozza` b JOIN (`effettua` e JOIN `docente` d ON e.rifDocente = d.id) ON e.rifBozza = b.id WHERE b.id LIKE :search OR d.nome LIKE :search OR e.ruolo LIKE :search";
        $stmtFiltered = $pdo->prepare($filteredRecordsQuery);
        $stmtFiltered->execute([":search" => $searchValue]);
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
} elseif ($action == "delete") {
    $rifB = $_POST["rif"];
    $stmt = $pdo->prepare("SELECT `validita` FROM `bozza` WHERE `id` = :id");
    $stmt -> execute(["id" => $rifB]);
    $valida = $stmt->fetchColumn();
    $stmt = $pdo->prepare("DELETE FROM `effettua` WHERE `rifBozza` = :rifB LIMIT 1");
    $stmt -> execute([":rifB" => $rifB]);
    if ($valida == "Sì") {
        $stmt = $pdo->prepare("SELECT `rifProposta` FROM `consegue` WHERE `rifBozza` = :rifB");
        $stmt -> execute([":rifB" => $rifB]);
        $rifV = $stmt->fetchColumn();
        $stmt = $pdo->prepare("DELETE FROM `consegue` WHERE `rifBozza` = :rifB LIMIT 1");
        $stmt -> execute([":rifB" => $rifB]);
        $stmt = $pdo->prepare("DELETE FROM `partecipa` WHERE `rifViaggio` = :rifV LIMIT 1");
        $stmt -> execute([":rifV" => $rifV]);
    }
}

