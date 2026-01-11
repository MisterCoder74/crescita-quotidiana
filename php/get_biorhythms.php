<?php
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

// User data folder convention: /data/users/{user_id}/
$userDir = DATA_DIR . '/users/' . $_SESSION['user_id'];

// Check if user directory exists, return empty array if not
if (!is_dir($userDir)) {
    echo json_encode([
        'success' => true,
        'biorhythms' => []
    ]);
    exit;
}

// Path to user's biorhythms file
$biorhythmsFile = $userDir . '/biorhythms.json';

// Return empty array if file doesn't exist
if (!file_exists($biorhythmsFile)) {
    echo json_encode([
        'success' => true,
        'biorhythms' => []
    ]);
    exit;
}

// Load and return biorhythms data
$content = file_get_contents($biorhythmsFile);
$biorhythms = json_decode($content, true);

if (!is_array($biorhythms)) {
    $biorhythms = [];
}

echo json_encode([
    'success' => true,
    'biorhythms' => $biorhythms
]);
?>
