<?php
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

require_once __DIR__ . '/config.php';

class BiorhythmCalculator {
private $physicalCycle = 23;
private $emotionalCycle = 28;
private $intellectualCycle = 33;

public function calculate($birthdate, $targetDate = null) {
if ($targetDate === null) {
$targetDate = date('Y-m-d');
}

$birthDateTime = new DateTime($birthdate);
$targetDateTime = new DateTime($targetDate);
$daysSinceBirth = $birthDateTime->diff($targetDateTime)->days;

return [
'physical' => $this->generateCycle($daysSinceBirth, $this->physicalCycle, 'physical'),
'emotional' => $this->generateCycle($daysSinceBirth, $this->emotionalCycle, 'emotional'),
'intellectual' => $this->generateCycle($daysSinceBirth, $this->intellectualCycle, 'intellectual')
];
}

private function generateCycle($daysSinceBirth, $cycleLength, $type) {
$radians = (2 * M_PI * $daysSinceBirth) / $cycleLength;
$value = sin($radians);
$percentage = round($value * 100);
$status = $this->getStatus($value);
$description = $this->getDescription($type, $status);

return [
'value' => $value,
'percentage' => $percentage,
'absPercentage' => abs($percentage),
'status' => ucfirst($status),
'description' => $description
];
}

private function getStatus($value) {
if ($value > 0.5) return 'high';
if ($value > 0) return 'rising';
if ($value > -0.5) return 'low';
return 'critical';
}

private function getDescription($type, $status) {
$descriptions = [
'physical' => [
'high' => 'Massima energia fisica e resistenza.',
'rising' => 'Crescita della forza fisica.',
'low' => 'Energia fisica ridotta.',
'critical' => 'Energia al minimo. Meglio riposare.'
],
'emotional' => [
'high' => 'Equilibrio emotivo e ottimo umore.',
'rising' => 'Migliora lo stato emotivo.',
'low' => 'Sensibilità maggiore.',
'critical' => 'Forte vulnerabilità emotiva.'
],
'intellectual' => [
'high' => 'Grande chiarezza mentale.',
'rising' => 'Crescente capacità cognitiva.',
'low' => 'Focalizzazione ridotta.',
'critical' => 'Stanchezza mentale.'
]
];
return $descriptions[$type][$status] ?? '';
}

public function getDominantCycle($biorhythms) {
$values = [
'physical' => abs($biorhythms['physical']['value']),
'emotional' => abs($biorhythms['emotional']['value']),
'intellectual' => abs($biorhythms['intellectual']['value'])
];
arsort($values);
return array_key_first($values);
}
}

// ✅ Gestione POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['birthdate'])) {
echo json_encode(['success' => false, 'error' => 'Birth date is required']);
exit;
}

try {
$calculator = new BiorhythmCalculator();
$biorhythms = $calculator->calculate($input['birthdate']);
$dominantCycle = $calculator->getDominantCycle($biorhythms);

// Logging dati utente
$userDir = DATA_DIR . '/users/' . $_SESSION['user_id'];
if (!is_dir($userDir)) {
mkdir($userDir, 0755, true);
}

$logFile = $userDir . '/biorhythm_log.json';

$logs = [];
if (file_exists($logFile)) {
$logs = json_decode(file_get_contents($logFile), true) ?: [];
}

$logs[] = [
'timestamp' => date('Y-m-d H:i:s'),
'birthdate' => $input['birthdate'],
'calculation_date' => date('Y-m-d'),
'dominant_cycle' => $dominantCycle
];

// Mantieni solo le ultime 100 voci
if (count($logs) > 100) {
$logs = array_slice($logs, -100);
}

file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo json_encode([
'success' => true,
'biorhythms' => $biorhythms,
'dominantCycle' => $dominantCycle,
'daysSinceBirth' => (new DateTime($input['birthdate']))->diff(new DateTime())->days
]);

} catch (Exception $e) {
echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

} else {
echo json_encode(['success' => false, 'error' => 'Only POST method allowed']);
}
?>