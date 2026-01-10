<?php
session_start(); // Avvia la sessione
session_unset(); // Rimuovi tutte le variabili di sessione
session_destroy(); // Distruggi la sessione

// Reindirizza l'utente a una pagina di login o alla homepage dopo il logout
header('Location: index.html');
exit;
?>