<?php
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// ✅ Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

// ✅ User folder path
$userDir = DATA_DIR . '/users/' . $_SESSION['user_id'];

// ✅ Path to journal file
$journalFile = $userDir . '/journal.json';

// ✅ Load entries (newest first)
if (file_exists($journalFile)) {
    $entries = json_decode(file_get_contents($journalFile), true) ?: [];
    
    // Sort by timestamp descending (newest first)
    usort($entries, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
    
    echo json_encode([
        'success' => true,
        'entries' => $entries
    ]);
} else {
    // No entries yet - return empty array
    echo json_encode([
        'success' => true,
        'entries' => []
    ]);
}
?>
