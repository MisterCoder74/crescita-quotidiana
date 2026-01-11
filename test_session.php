<?php
// Test script to simulate user session and test multi-user functionality
require_once __DIR__ . '/config.php';

session_start();

// Set up a test user session
$_SESSION['user_id'] = 'user_test_123';
$_SESSION['google_id'] = 'google_test_456';
$_SESSION['email'] = 'test@example.com';
$_SESSION['name'] = 'Test User';
$_SESSION['profile_picture'] = 'https://example.com/pic.jpg';
$_SESSION['user_folder'] = 'user_test_123';
$_SESSION['user_folder_path'] = DATA_DIR . '/users/user_test_123';

echo "Session setup complete:\n";
echo "user_id: " . $_SESSION['user_id'] . "\n";
echo "user_folder: " . $_SESSION['user_folder'] . "\n";
echo "user_folder_path: " . $_SESSION['user_folder_path'] . "\n";

// Test user folder creation
$userDir = DATA_DIR . '/users/' . $_SESSION['user_id'];
echo "Testing user directory creation: $userDir\n";

if (!is_dir($userDir)) {
    mkdir($userDir, 0755, true);
    echo "Created user directory\n";
} else {
    echo "User directory already exists\n";
}

echo "Directory contents:\n";
print_r(scandir($userDir));

// Test task endpoint
echo "\n\nTesting tasks endpoint...\n";
ob_start();
include 'tasks.php';
$output = ob_get_clean();
echo "Tasks output: " . $output . "\n";

// Test emotions endpoint
echo "\n\nTesting emotions endpoint...\n";
ob_start();
include 'php/get_emotions.php';
$output = ob_get_clean();
echo "Emotions output: " . $output . "\n";

?>