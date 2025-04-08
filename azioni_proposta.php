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
    $columns = ["id", "descrizione"];

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
    $query = "SELECT * FROM `proposta`";

    // Aggiunta filtro di ricerca
    if (!empty($searchValue))
        $query .= " WHERE id LIKE :search";

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
    $totalRecordsQuery = "SELECT COUNT(*) FROM `proposta`";
    $totalRecords = $pdo->query($totalRecordsQuery)->fetchColumn();

    // Conteggio totale con filtro
    if (!empty($searchValue)) {
        $filteredRecordsQuery = "SELECT COUNT(*) FROM `proposta`";
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
    $stmt = $pdo->prepare("SELECT proposta.id, proposta.descrizione, viaggio.nome, mezzo, destinazione, classe.nome as classe, numerosita FROM `proposta` LEFT JOIN (`viaggio` LEFT JOIN (`coinvolge` LEFT JOIN (`classe` LEFT JOIN `indirizzo` ON classe.rifIndirizzo = indirizzo.id) ON coinvolge.rifClasse = classe.id) ON coinvolge.rifViaggio = viaggio.id) ON proposta.id = viaggio.id WHERE proposta.id = :id");
    $stmt -> execute([":id" => $id]);
    echo json_encode($stmt->fetch());

// Salvataggio bozza
} elseif ($action == "save") {

    // Dati viaggio
    $id = $_POST["draftId"];
    $descrizione = $_POST["descrizione"];
    $nome = $_POST["nome"];
    $tipo = $_POST["tipo"];
    $dataInizio = $_POST["dataInizio"];
    $dataInizio = strtotime($dataInizio);
    $dataInizio = date("d/m/Y H:i", $dataInizio);
    $dataFine = $_POST["dataFine"];
    $dataFine = strtotime($dataFine);
    $dataFine = date("d/m/Y H:i", $dataFine);
    $mezzo = $_POST["mezzo"];
    $destinazione = $_POST["destinazione"];

    // Dati classi
    $numClasse = $_POST["numClasse"];
    $sezClasse = $_POST["sezClasse"];
    $classe = $numClasse.$sezClasse;
    $indirizzo = $_POST["indirizzo"];
    $userId = $_POST["userId"];
    $ruolo = $_POST["ruolo"];
    $coordinatore = $_POST["coordinatore"];
    $numerosita = (int) $_POST["numerosita"];

    // Viaggio
    $stmt = $pdo->prepare("UPDATE `proposta` SET `descrizione` = :descrizione WHERE `id` = :id");
    $stmt -> execute([":descrizione" => $descrizione, ":id" => $id]);
    $stmt = $pdo->prepare("UPDATE `viaggio` SET `nome` = :nome, `dataInizio` = :dataInizio, `dataFine` = :dataFine, `mezzo` = :mezzo, `destinazione` = :destinazione, `rifTipo` = :tipo WHERE `id` = :id");
    $stmt -> execute([":nome" => $nome, ":dataInizio" => $dataInizio, ":dataFine" => $dataFine, ":mezzo" => $mezzo, ":destinazione" => $destinazione, ":tipo" => $tipo, ":id" => $id]);

    // Classe
    $stmt = $pdo->prepare("INSERT INTO `classe` (`nome`, `numerosita`, `dueTerzi`, `rifDocente`, `rifIndirizzo`) VALUES (:classe, :numerosita, :dueTerzi, :rifD, :rifI)");
    $stmt -> execute([":classe" => $classe, ":numerosita" => $numerosita, ":dueTerzi" => ($numerosita/3) * 2, ":rifD" => $coordinatore, ":rifI" => $indirizzo]);
    $stmt = $pdo->prepare("SELECT MAX(`id`) FROM `classe`");
    $stmt -> execute();
    $rifC = $stmt->fetchColumn();
    $stmt = $pdo->prepare("INSERT INTO `coinvolge` (`rifViaggio`, `rifClasse`) VALUES (:rifV, :rifC)");
    $stmt -> execute([":rifV" => $id, ":rifC" => $rifC]);

    // Storico
    $stmt = $pdo->prepare("SELECT `id` FROM `docente` WHERE `rifUtente` = :rif");
    $stmt -> execute([":rif" => $userId]);
    $rifD = $stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT `rifBozza` FROM `consegue` WHERE `rifProposta` = :rifP");
    $stmt -> execute([":rifP" => $id]);
    $rifB = $stmt->fetchColumn();
    $stmt = $pdo->prepare("INSERT INTO `consegue` (`data`, `rifProposta`, `rifBozza`) VALUES (:data, :rifP, :rifB)");
    $stmt -> execute([":data" => date("d/m/Y H:i:s"), ":rifP" => $id, ":rifB" => $rifB]);
    $stmt = $pdo->prepare("INSERT INTO `partecipa` (`ruolo`, `rifViaggio`, `rifDocente`) VALUES (:ruolo, :rifV, :rifD)");
    $stmt -> execute([":ruolo" => $ruolo, ":rifV" => $id, ":rifD" => $rifD]);
}