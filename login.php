<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$email = $_POST['email'];
$password = $_POST['password'];

// Carica gli utenti
if (file_exists('./data/setup.json')) {
$data = json_decode(file_get_contents('./data/setup.json'), true);
foreach ($data as $user) {
if ($user['email'] === $email) {
if (password_verify($password, $user['password'])) {
$_SESSION['username'] = $user['username'];
$_SESSION['birthdate'] = $user['birthdate'];
header('Location: dashboard.html');
exit();
} else {
echo "Password errata!";
exit();
}
}
}
}
echo "Nome utente non trovato!";
}