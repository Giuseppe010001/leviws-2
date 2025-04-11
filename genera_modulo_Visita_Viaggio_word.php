<?php
$File = 'Modulo_Richiesta_Visita_Viaggio.docx';

if (file_exists($File)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . basename($File) . '"');
    header('Content-Length: ' . filesize($File));
    flush(); // Pulisce l'output buffer
    readfile($File);
    exit;
} else {
    echo "File non trovato.";
}
?>