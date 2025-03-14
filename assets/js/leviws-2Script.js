// Chiamata della funzione di gestione dell'effetto di transizione
$(document).ready(transizioneFine);

// Funzione finalizzata ad implementare l'entrata in dissolvenza dei loghi
function transizioneFine() {
    $(".transizioneInizio").delay(2000).fadeIn(3000);
}