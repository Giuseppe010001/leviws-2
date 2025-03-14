<?php
require('includes/FPDF-master/fpdf.php');

$nome_referente = $_POST["nomeReferente"];
$classe_riferita = $_POST["classeRiferita"];
$data_partenza = date("d/m/Y", strtotime($_POST["dataPartenza"]));
$data_arrivo = date("d/m/Y", strtotime($_POST["dataArrivo"]));
$data_compilazione = date("d/m/Y", strtotime($_POST["dataCompilazione"])); // Conversione formato ITA
$descrizione = $_POST["descrizione"];

// Imposta gli headers per forzare il download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Relazione.pdf"');

// Creazione del documento PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 13);

$pdf->Image('images/logoLevi.png', 10, 10, 90);
$pdf->Image('images/logoFutura.png', 110, 10, 90);

// Sposta la scrittura sotto l'immagine
$pdf->Ln(80);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, "Nome referente:", 0, 0);
$pdf->Cell(100, 10, $nome_referente, 0, 1);

$pdf->Cell(50, 10, "Classe riferita:", 0, 0);
$pdf->Cell(100, 10, $classe_riferita, 0, 1);

$pdf->Cell(50, 10, "Data di partenza:", 0, 0);
$pdf->Cell(100, 10, $data_partenza, 0, 1);

$pdf->Cell(50, 10, "Data di arrivo:", 0, 0);
$pdf->Cell(100, 10, $data_arrivo, 0, 1);

$pdf->Cell(50, 10, "Descrizione:", 0, 0);
$pdf->Cell(100, 10, $descrizione, 0, 1);

$pdf->Ln(5);
$pdf->Cell(50, 10, "Vignola, " . $data_compilazione, 0, 1);

// Impostare l'output per il download all'utente
$pdf->Output('D', 'Relazione.pdf');
?>
