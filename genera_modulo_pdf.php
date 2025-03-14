<?php
$file = 'Modulo_Richiesta_Visita.pdf';

if (file_exists($file)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename=' . $file);
    readfile($file);
    exit;
} else {
    echo "Il file non esiste.";
}
?>