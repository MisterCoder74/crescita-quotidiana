<?php
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// ✅ Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

// ✅ Validate input
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['content']) || empty(trim($data['content']))) {
    http_response_code(400);
    echo json_encode(['error' => 'Content is required']);
    exit;
}

if (!isset($data['gradient']) || empty($data['gradient'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Gradient color is required']);
    exit;
}

// ✅ User folder path
$userDir = DATA_DIR . '/users/' . $_SESSION['user_id'];

// ✅ Create user folder if doesn't exist
if (!is_dir($userDir)) {
    mkdir($userDir, 0755, true);
}

// ✅ Path to journal file
$journalFile = $userDir . '/journal.json';

// ✅ Load existing entries
$entries = [];
if (file_exists($journalFile)) {
    $entries = json_decode(file_get_contents($journalFile), true) ?: [];
}

// ✅ Create new entry
$newEntry = [
    'id' => uniqid('journal_', true),
    'content' => strip_tags(trim($data['content'])),
    'gradient' => htmlspecialchars($data['gradient'], ENT_QUOTES, 'UTF-8'),
    'timestamp' => date('c'),
    'date' => date('Y-m-d'),
    'time' => date('H:i')
];

// ✅ Add to entries array
$entries[] = $newEntry;

// ✅ Save to file
if (file_put_contents(
    $journalFile,
    json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
)) {
    echo json_encode([
        'success' => true,
        'message' => 'Diario salvato con successo',
        'entry' => $newEntry
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save journal entry']);
}
?>
