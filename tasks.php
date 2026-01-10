<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
exit(0);
}

// ✅ Controlla autenticazione
if (!isset($_SESSION['user_id'])) {
http_response_code(403);
echo json_encode(['error' => 'Not authorized']);
exit;
}

require_once __DIR__ . '/config.php';

// Percorso cartella utente
$userDir = DATA_DIR . '/users/' . $_SESSION['user_id'];

// Crea la cartella utente se non esiste
if (!is_dir($userDir)) {
mkdir($userDir, 0755, true);
}

// Percorso del file dei task dell’utente
$tasksFile = $userDir . '/tasks.json';

// Se il file non esiste, crealo
if (!file_exists($tasksFile)) {
file_put_contents($tasksFile, json_encode([]));
}

// --- Funzioni di supporto ---

function loadTasks($tasksFile) {
$content = file_get_contents($tasksFile);
return json_decode($content, true) ?: [];
}

function saveTasks($tasksFile, $tasks) {
return file_put_contents($tasksFile, json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function validateTask($task) {
if (empty($task['title']) || empty($task['date'])) {
return false;
}
// Controlla formato data
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $task['date'])) {
return false;
}
// Controlla formato ora se presente
if (!empty($task['time']) && !preg_match('/^\d{2}:\d{2}$/', $task['time'])) {
return false;
}
// Controlla priorità
if (!in_array($task['priority'], ['low', 'medium', 'high'])) {
return false;
}
return true;
}

// --- Logica principale ---
try {
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
// Carica tutti i task
if (isset($_GET['action']) && $_GET['action'] === 'load') {
$tasks = loadTasks($tasksFile);
echo json_encode([
'success' => true,
'tasks' => $tasks
]);
} else {
echo json_encode([
'success' => false,
'message' => 'Invalid action'
]);
}
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
throw new Exception('Invalid input data');
}

switch ($input['action']) {
case 'save':
if (!isset($input['task'])) {
throw new Exception('Task data is required');
}

$task = $input['task'];

if (!validateTask($task)) {
throw new Exception('Invalid task data');
}

// Sanitize input
$task['title'] = strip_tags(trim($task['title']));
$task['description'] = strip_tags(trim($task['description'] ?? ''));
$task['time'] = trim($task['time'] ?? '');
$task['priority'] = trim($task['priority']);
$task['created_at'] = date('Y-m-d H:i:s');
$task['id'] = uniqid();

$allTasks = loadTasks($tasksFile);

if (!isset($allTasks[$task['date']])) {
$allTasks[$task['date']] = [];
}

$allTasks[$task['date']][] = $task;

// Ordina per ora
usort($allTasks[$task['date']], function($a, $b) {
if (empty($a['time']) && empty($b['time'])) return 0;
if (empty($a['time'])) return 1;
if (empty($b['time'])) return -1;
return strcmp($a['time'], $b['time']);
});

if (saveTasks($tasksFile, $allTasks)) {
echo json_encode([
'success' => true,
'message' => 'Task saved successfully',
'task' => $task
]);
} else {
throw new Exception('Failed to save task');
}
break;

case 'delete':
if (!isset($input['date']) || !isset($input['task_id'])) {
throw new Exception('Date and task ID are required');
}

$date = $input['date'];
$taskId = $input['task_id'];
$allTasks = loadTasks($tasksFile);

if (isset($allTasks[$date])) {
$allTasks[$date] = array_filter($allTasks[$date], fn($task) => $task['id'] !== $taskId);

if (empty($allTasks[$date])) {
unset($allTasks[$date]);
}

if (saveTasks($tasksFile, $allTasks)) {
echo json_encode([
'success' => true,
'message' => 'Task deleted successfully'
]);
} else {
throw new Exception('Failed to delete task');
}
} else {
throw new Exception('Task not found');
}
break;

case 'update':
if (!isset($input['task']) || !isset($input['task']['id'])) {
throw new Exception('Task data with ID is required');
}

$task = $input['task'];

if (!validateTask($task)) {
throw new Exception('Invalid task data');
}

// Sanitize input
$task['title'] = strip_tags(trim($task['title']));
$task['description'] = strip_tags(trim($task['description'] ?? ''));
$task['time'] = trim($task['time'] ?? '');
$task['priority'] = trim($task['priority']);
$task['updated_at'] = date('Y-m-d H:i:s');

$allTasks = loadTasks($tasksFile);
$found = false;

foreach ($allTasks as $date => &$dateTasks) {
foreach ($dateTasks as &$existingTask) {
if ($existingTask['id'] === $task['id']) {
$existingTask = array_merge($existingTask, $task);
$found = true;
break 2;
}
}
}

if (!$found) {
throw new Exception('Task not found');
}

if (saveTasks($tasksFile, $allTasks)) {
echo json_encode([
'success' => true,
'message' => 'Task updated successfully',
'task' => $task
]);
} else {
throw new Exception('Failed to update task');
}
break;

default:
throw new Exception('Invalid action');
}
} else {
throw new Exception('Method not allowed');
}

} catch (Exception $e) {
http_response_code(400);
echo json_encode([
'success' => false,
'message' => $e->getMessage()
]);
}
?>