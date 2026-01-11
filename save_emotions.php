<?php
require_once __DIR__ . '/config.php';
session_start();

// ✅ Controllo autenticazione
if (!isset($_SESSION['user_id'])) {
http_response_code(403);
header('Content-Type: application/json');
echo json_encode(['error' => 'Not authorized']);
exit;
}

// ✅ Lettura e validazione dei dati in input
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !is_array($data)) {
http_response_code(400);
echo json_encode(['error' => 'Invalid input data']);
exit;
}

// ✅ User data folder convention: /data/users/{user_id}/
// All user-specific data must be stored using $_SESSION['user_id']
$userDir = DATA_DIR . '/users/' . $_SESSION['user_id'];

// ✅ Crea la cartella utente se non esiste
if (!is_dir($userDir)) {
mkdir($userDir, 0755, true);
}

// ✅ Nome del file JSON per le emozioni
$jsonFile = $userDir . '/emotions.json';

// ✅ Leggi dati esistenti
if (file_exists($jsonFile)) {
$jsonData = json_decode(file_get_contents($jsonFile), true);
if (!is_array($jsonData)) {
$jsonData = [];
}
} else {
$jsonData = [];
}

// ✅ Aggiungi il nuovo record con timestamp
$data['timestamp'] = date('c');
$jsonData[] = $data;

// ✅ Salva nel file JSON dell’utente
file_put_contents(
$jsonFile,
json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

// ✅ Risposta JSON di successo
header('Content-Type: application/json');
echo json_encode(['success' => true]);