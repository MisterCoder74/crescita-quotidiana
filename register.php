<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$username = $_POST['username'];
$email = $_POST['email'];        
$password = $_POST['password'];
$birthdate = $_POST['birthdate'];

// Carica i dati esistenti
$data = [];
if (file_exists('./data/setup.json')) {
$data = json_decode(file_get_contents('./data/setup.json'), true);
}

// Controlla se l'email è già stato utilizzato
foreach ($data as $user) {
if ($user['email'] === $email) {
echo "Email già esistente.";
exit();
}
}

// Salva l'utente
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$data[] = [
'username' => $username,
'email' => $email,        
'password' => $hashed_password,
'birthdate' => $birthdate
];

// Scrivi nel file JSON
file_put_contents('./data/setup.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Registrazione avvenuta con successo!";
}
?>