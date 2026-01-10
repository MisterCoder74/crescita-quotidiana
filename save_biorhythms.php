<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Leggi i dati JSON inviati
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Dati non validi');
    }
    
    // Nome del file JSON
    $filename = './data/biorhythms_data.json';
    
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
    
    // Salva nel file JSON
    $jsonString = json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($filename, $jsonString) !== false) {
        echo json_encode(['success' => true, 'message' => 'Dati salvati correttamente']);
    } else {
        throw new Exception('Impossibile scrivere sul file');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>