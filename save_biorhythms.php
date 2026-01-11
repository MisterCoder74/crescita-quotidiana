<?php
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// ✅ Controllo autenticazione
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit;
}

try {
    // Leggi i dati JSON inviati
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Dati non validi');
    }
    
    // ✅ User data folder convention: /data/users/{user_id}/
    // All user-specific data must be stored using $_SESSION['user_id']
    $userDir = DATA_DIR . '/users/' . $_SESSION['user_id'];
    
    // Crea la cartella utente se non esiste
    if (!is_dir($userDir)) {
        mkdir($userDir, 0755, true);
    }
    
    // Nome del file JSON user-specific
    $filename = $userDir . '/biorhythms.json';
    
    // Leggi i dati esistenti o crea array vuoto
    $existingData = [];
    if (file_exists($filename)) {
        $jsonContent = file_get_contents($filename);
        $existingData = json_decode($jsonContent, true) ?: [];
    }
    
    // Aggiungi timestamp per identificazione univoca
    $data['id'] = uniqid();
    $data['timestamp'] = date('Y-m-d H:i:s');
    
    // Aggiungi i nuovi dati
    $existingData[] = $data;
    
    // Salva nel file JSON dell'utente
    $jsonString = json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($filename, $jsonString) !== false) {
        echo json_encode(['success' => true, 'message' => 'Dati salvati correttamente']);
    } else {
        throw new Exception('Impossibile scrivere sul file');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>