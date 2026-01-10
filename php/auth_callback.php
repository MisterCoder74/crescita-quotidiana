<?php
require_once __DIR__ . '/config.php';
session_start();

function handleError($message) {
echo "<!DOCTYPE html>
<html>
<head>
<title>Authentication Error</title>
<style>
body { font-family: Arial, sans-serif; max-width: 600px; margin: 100px auto; padding: 20px; }
.error { background: #ffebee; border: 1px solid #ef5350; border-radius: 4px; padding: 20px; color: #c62828; }
a { color: #1976d2; }
</style>
</head>
<body>
<div class='error'>
<h2>Authentication Error</h2>
<p>" . htmlspecialchars($message) . "</p>
<p><a href=\"../index.html\">Return to login</a></p>
</div>
</body>
</html>";
exit;
}

if (!isset($_GET['code'])) {
handleError('No authorization code received from Google.');
}

$authCode = $_GET['code'];

// Exchange code for token
$tokenData = [
'code' => $authCode,
'client_id' => OAUTH_CLIENT_ID,
'client_secret' => OAUTH_CLIENT_SECRET,
'redirect_uri' => OAUTH_REDIRECT_URI,
'grant_type' => 'authorization_code'
];

$ch = curl_init(OAUTH_TOKEN_ENDPOINT);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$tokenResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
handleError('Failed to authenticate with Google. Please try again.');
}

$tokenJson = json_decode($tokenResponse, true);
if (!isset($tokenJson['access_token'])) {
handleError('Invalid response from Google.');
}

$accessToken = $tokenJson['access_token'];

// Fetch user info
$ch = curl_init(OAUTH_USERINFO_ENDPOINT . '?access_token=' . urlencode($accessToken));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$userInfoResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
handleError('Failed to retrieve user information from Google.');
}

$userInfo = json_decode($userInfoResponse, true);

if (!isset($userInfo['id']) || !isset($userInfo['email'])) {
handleError('Incomplete user information from Google.');
}

// Load or create user JSON
$usersFile = DATA_DIR . '/users.json';
if (!is_dir(DATA_DIR)) {
mkdir(DATA_DIR, 0755, true);
}

$users = [];
if (file_exists($usersFile)) {
$content = file_get_contents($usersFile);
$data = json_decode($content, true);
$users = $data['users'] ?? [];
}

$googleId = $userInfo['id'];
$email = $userInfo['email'];
$name = $userInfo['name'] ?? $email;
$picture = $userInfo['picture'] ?? '';

$user = null;
foreach ($users as $u) {
if ($u['google_id'] === $googleId) {
$user = $u;
break;
}
}

if (!$user) {
// New user
$user = [
'id' => uniqid('user_', true),
'google_id' => $googleId,
'email' => $email,
'name' => $name,
'picture' => $picture,
'created_at' => date('c'),
'last_login' => date('c'),
// nuovi campi
'age' => null,
'city' => '',
'user_folder' => '',
'user_level' => 'basic'
];
$users[] = $user;
} else {
// Update last login
foreach ($users as &$u) {
if ($u['id'] === $user['id']) {
$u['last_login'] = date('c');
break;
}
}
}

file_put_contents($usersFile, json_encode(['users' => $users], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// === Gestione directory utente ===
$rootUsersDir = ROOT_DIR . '/users';
if (!is_dir($rootUsersDir)) {
mkdir($rootUsersDir, 0755, true);
}

// nome cartella utente: puoi usare google_id o id interno
$userFolderName = $user['google_id']; // oppure $user['id']
$userFolderPath = $rootUsersDir . '/' . $userFolderName;

if (!is_dir($userFolderPath)) {
mkdir($userFolderPath, 0755, true);
}

// === Aggiori eventualmente il campo user_folder nel JSON ===
foreach ($users as &$u) {
if ($u['id'] === $user['id']) {
$u['user_folder'] = $userFolderName;
break;
}
}
file_put_contents($usersFile, json_encode(['users' => $users], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// === Imposta tutte le variabili di sessione ===
$_SESSION['user_id'] = $user['id'];
$_SESSION['google_id'] = $user['google_id'];
$_SESSION['email'] = $user['email'];
$_SESSION['name'] = $user['name'];
$_SESSION['profile_picture'] = $user['picture'];
$_SESSION['user_folder'] = $userFolderName; // nome della cartella
$_SESSION['user_folder_path'] = $userFolderPath; // percorso assoluto

session_regenerate_id(true);

// Redirect alla dashboard
header('Location: ../dashboard.php');
exit;
