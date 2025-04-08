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
    $columns = ["rif", "classe", "indirizzo", "coordinatore", "numerosita", "due_terzi"];

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
    $query = "SELECT `rifViaggio` as rif, cl.nome as classe, i.descrizione as indirizzo, d.nome as coordinatore, `numerosita`, `dueTerzi` as due_terzi FROM `docente` d JOIN (`coinvolge` co JOIN (`classe` cl JOIN `indirizzo` i ON cl.rifIndirizzo = i.id) ON co.rifClasse = cl.id) ON cl.rifDocente = d.id";

    // Aggiunta filtro di ricerca
    if (!empty($searchValue))
        $query .= " WHERE rifViaggio LIKE :search OR cl.nome LIKE :search OR i.descrizione LIKE :search OR d.nome LIKE :search OR numerosita LIKE :search OR dueTerzi LIKE :search";

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

    // Conteggio totale dei record\
    $totalRecordsQuery = "SELECT COUNT(*) FROM `classe`";
    $totalRecords = $pdo->query($totalRecordsQuery)->fetchColumn();

    // Conteggio totale con filtro
    if (!empty($searchValue)) {
        $filteredRecordsQuery = "SELECT COUNT(*) FROM `docente` d JOIN (`coinvolge` co JOIN (`classe` cl JOIN `indirizzo` i ON cl.rifIndirizzo = i.id) ON co.rifClasse = cl.id) ON cl.rifDocente = d.id WHERE rifViaggio LIKE :search OR cl.nome LIKE :search OR i.descrizione LIKE :search OR d.nome LIKE :search OR numerosita LIKE :search OR dueTerzi LIKE :search";
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
}