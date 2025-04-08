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
    $columns = ["id", "nome", "descrizione", "valida"];

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
    $query = "SELECT `id`, `nome`, `descrizione`, `validita` as `valida` FROM `bozza`";

    // Aggiunta filtro di ricerca
    if (!empty($searchValue))
        $query .= " WHERE nome LIKE :search";

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
    $totalRecordsQuery = "SELECT COUNT(*) FROM `bozza`";
    $totalRecords = $pdo->query($totalRecordsQuery)->fetchColumn();

    // Conteggio totale con filtro
    if (!empty($searchValue)) {
        $filteredRecordsQuery = "SELECT COUNT(*) FROM `bozza` WHERE `nome` LIKE :search";
        $stmtFiltered = $pdo->prepare($filteredRecordsQuery);
        $stmtFiltered -> execute([":search" => $searchValue]);
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

// Modifica bozza
} elseif ($action == "edit") {
    $id = $_GET["id"];
    $stmt = $pdo->prepare("SELECT * FROM `bozza` WHERE `id` = :id");
    $stmt -> execute([":id" => $id]);
    echo json_encode($stmt->fetch());

// Salvataggio bozza
} elseif ($action == "save") {
    $id = $_POST["draftId"] ?? null;
    $userId = $_POST["userId"];
    $nome = $_POST["nome"];
    $descrizione = $_POST["descrizione"];
    $ruolo = $_POST["ruolo"];
    $valida = $_POST["valida"] ?? "No";

    // Ricavare il docente autore
    $stmt = $pdo->prepare("SELECT `id` FROM `docente` WHERE `rifUtente` = :rif");
    $stmt -> execute([":rif" => $userId]);
    $rifD = $stmt->fetchColumn();

    if ($id) {

        // Aggiornamento bozza
        $stmt = $pdo->prepare("UPDATE `bozza` SET `nome` = :nome, `descrizione` = :descrizione, `validita` = :validita WHERE `id` = :id");
        $stmt -> execute([":nome" => $nome, ":descrizione" => $descrizione, ":validita" => $valida, ":id" => $id]);
        $stmt = $pdo->prepare("INSERT INTO `effettua` (`data`, `ruolo`, `rifDocente`, `rifBozza`) VALUES (:data, :ruolo, :rifD, :rifB)");
        $stmt -> execute([":data" => date("d/m/Y H:i:s", time()), ":ruolo" => $ruolo, ":rifD" => $rifD, ":rifB" => $id]);
        $stmt = $pdo->prepare("SELECT `rifProposta` FROM `consegue` WHERE `rifBozza` = :rifB");
        $stmt -> execute([":rifB" => $id]);
        $rifP = $stmt->fetchColumn();
        $rifV = $rifP;
        $stmt = $pdo->prepare("DELETE FROM `consegue` WHERE `rifBozza` = :rifB");
        $stmt -> execute([":rifB" => $id]);
        $stmt = $pdo->prepare("DELETE FROM `partecipa` WHERE `rifViaggio` = :rifV");
        $stmt -> execute([":rifV" => $rifV]);
        $stmt = $pdo->prepare("DELETE FROM `proposta` WHERE `id` = :id");
        $stmt -> execute([":id" => $rifP]);
        $stmt = $pdo->prepare("DELETE FROM `viaggio` WHERE `id` = :id");
        $stmt -> execute([":id" => $rifP]);
        if ($valida == "Sì") {
            $stmt = $pdo->prepare("INSERT INTO `proposta` (`descrizione`) VALUES (:descrizione)");
            $stmt -> execute([":descrizione" => $descrizione]);
            $stmt = $pdo->prepare("SELECT MAX(`id`) FROM `proposta`");
            $stmt -> execute();
            $rifP = $stmt->fetchColumn();
            $rifV = $rifP;
            $stmt = $pdo->prepare("INSERT INTO `viaggio` (`id`, `nome`) VALUES (:id, :nome)");
            $stmt -> execute([":id" => $rifP, ":nome" => $nome]);
            $stmt = $pdo->prepare("INSERT INTO `consegue` (`data`, `rifProposta`, `rifBozza`) VALUES (:data, :rifP, :rifB)");
            $stmt -> execute([":data" => date("d/m/Y H:i:s", time()), ":rifP" => $rifP, ":rifB" => $id]);
            $stmt = $pdo->prepare("INSERT INTO `partecipa` (`ruolo`, `rifViaggio`, `rifDocente`) VALUES (:ruolo, :rifV, :rifD)");
            $stmt -> execute([":ruolo" => $ruolo, ":rifV" => $rifV, ":rifD" => $rifD]);
        }

    } else {

        // Creazione bozza
        $stmt = $pdo->prepare("INSERT INTO `bozza` (`nome`, `descrizione`, `validita`) VALUES (:nome, :descrizione, :validita)");
        $stmt -> execute([":nome" => $nome, ":descrizione" => $descrizione, ":validita" => $valida]);
        $stmt = $pdo->prepare("SELECT MAX(`id`) FROM `bozza`");
        $stmt -> execute();
        $rifB = $stmt->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO `effettua` (`data`, `ruolo`, `rifDocente`, `rifBozza`) VALUES (:data, :ruolo, :rifD, :rifB)");
        $stmt -> execute([":data" => date("d/m/Y H:i:s", time()), ":ruolo" => $ruolo, ":rifD" => $rifD, ":rifB" => $rifB]);
        if ($valida == "Sì") {
            $stmt = $pdo->prepare("INSERT INTO `proposta` (`descrizione`) VALUES (:descrizione)");
            $stmt -> execute([":descrizione" => $descrizione]);
            $stmt = $pdo->prepare("SELECT MAX(`id`) FROM `proposta`");
            $stmt -> execute();
            $rifP = $stmt->fetchColumn();
            $rifV = $rifP;
            $stmt = $pdo->prepare("INSERT INTO `viaggio` (`id`, `nome`) VALUES (:id, :nome)");
            $stmt -> execute([":id" => $rifP, ":nome" => $nome]);
            $stmt = $pdo->prepare("INSERT INTO `consegue` (`data`, `rifProposta`, `rifBozza`) VALUES (:data, :rifP, :rifB)");
            $stmt -> execute([":data" => date("d/m/Y H:i:s", time()), ":rifP" => $rifP, ":rifB" => $rifB]);
            $stmt = $pdo->prepare("INSERT INTO `partecipa` (`ruolo`, `rifViaggio`, `rifDocente`) VALUES (:ruolo, :rifV, :rifD)");
            $stmt -> execute([":ruolo" => $ruolo, ":rifV" => $rifV, ":rifD" => $rifD]);
        }
    }

// Eliminazione bozza
} elseif ($action == "delete") {
    $id = $_POST["id"];
    $stmt = $pdo->prepare("DELETE FROM `effettua` WHERE `rifBozza` = :rifB");
    $stmt -> execute([":rifB" => $id]);
    $stmt = $pdo->prepare("DELETE FROM `bozza` WHERE `id` = :id");
    $stmt -> execute([":id" => $id]);
}