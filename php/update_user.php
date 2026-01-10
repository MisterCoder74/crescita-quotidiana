<?php
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
http_response_code(403);
echo json_encode(['error' => 'Not authorized']);
exit;
}

$usersFile = DATA_DIR . '/users.json';
if (!file_exists($usersFile)) {
http_response_code(500);
echo json_encode(['error' => 'User data file missing']);
exit;
}

$data = json_decode(file_get_contents($usersFile), true);
$users = $data['users'] ?? [];

foreach ($users as &$u) {
if ($u['id'] === $_SESSION['user_id']) {

// Aggiorna nuovi campi del profilo
if (isset($_POST['data_nascita']))
$u['data_nascita'] = trim($_POST['data_nascita']);

if (isset($_POST['ora_nascita']))
$u['ora_nascita'] = trim($_POST['ora_nascita']);

if (isset($_POST['citta_nascita']))
$u['citta_nascita'] = trim($_POST['citta_nascita']);

// Aggiorna timestamp
$u['updated_at'] = date('c');

// (Facoltativo) aggiorna la cartella utente se necessario
$userDir = DATA_DIR . '/users/' . $_SESSION['user_id'];
if (!is_dir($userDir)) {
mkdir($userDir, 0755, true);
}

// Salva anche un file JSON individuale se serve
file_put_contents($userDir . '/profile.json', json_encode($u, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

break;
}
}

file_put_contents(
$usersFile,
json_encode(['users' => $users], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

echo json_encode(['success' => true]);