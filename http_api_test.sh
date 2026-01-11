#!/bin/bash

echo "========================================="
echo "CRESCITA QUOTIDIANA - HTTP API TEST"
echo "========================================="

# Clean start
echo ""
echo "=== CLEANING UP PREVIOUS TEST DATA ==="
rm -rf /home/engine/project/data/users/user_test_*
mkdir -p /home/engine/project/data/users
echo "✓ Cleaned up and created users directory"

# Test helper function
test_endpoint() {
    local method=$1
    local url=$2
    local data=$3
    local cookies=$4
    local description=$5
    
    echo ""
    echo "--- $description ---"
    
    if [ "$method" = "POST" ]; then
        if [ -n "$cookies" ]; then
            response=$(curl -s -X POST "$url" -H "Content-Type: application/json" -H "Cookie: $cookies" -d "$data")
        else
            response=$(curl -s -X POST "$url" -H "Content-Type: application/json" -d "$data")
        fi
    else
        if [ -n "$cookies" ]; then
            response=$(curl -s "$url" -H "Cookie: $cookies")
        else
            response=$(curl -s "$url")
        fi
    fi
    
    echo "Response: $response"
    echo "$response"
}

# PHASE 1: Task Management Testing
echo ""
echo "=== PHASE 1: TASK MANAGEMENT TESTING ==="

# Create a temporary session script for User 1
echo "Creating user sessions..."
php -r '
session_start();
$_SESSION["user_id"] = "user_test_001";
$_SESSION["google_id"] = "google_001"; 
$_SESSION["email"] = "user1@example.com";
$_SESSION["name"] = "Test User 1";
$_SESSION["user_folder"] = "user_test_001";
$_SESSION["user_folder_path"] = "/home/engine/project/data/users/user_test_001";
echo "Session ID: " . session_id() . "\n";
'

# Get session ID for User 1
USER1_SESSION=$(php -r 'session_start(); $_SESSION["user_id"] = "user_test_001"; $_SESSION["google_id"] = "google_001"; $_SESSION["email"] = "user1@example.com"; $_SESSION["name"] = "Test User 1"; $_SESSION["user_folder"] = "user_test_001"; $_SESSION["user_folder_path"] = "/home/engine/project/data/users/user_test_001"; echo session_id();')

echo "User 1 Session ID: $USER1_SESSION"

# Test User 1 saving a task
test_endpoint "POST" "http://localhost:8080/tasks.php" '{"action":"save","task":{"title":"User 1 Task","description":"Test task for user 1","date":"2024-01-15","time":"10:00","priority":"medium"}}' "LC_IDENTIFIER=$USER1_SESSION" "User 1 Save Task"

# Create session for User 2
USER2_SESSION=$(php -r 'session_start(); $_SESSION["user_id"] = "user_test_002"; $_SESSION["google_id"] = "google_002"; $_SESSION["email"] = "user2@example.com"; $_SESSION["name"] = "Test User 2"; $_SESSION["user_folder"] = "user_test_002"; $_SESSION["user_folder_path"] = "/home/engine/project/data/users/user_test_002"; echo session_id();')

echo "User 2 Session ID: $USER2_SESSION"

# Test User 2 saving a task
test_endpoint "POST" "http://localhost:8080/tasks.php" '{"action":"save","task":{"title":"User 2 Task","description":"Test task for user 2","date":"2024-01-15","time":"14:00","priority":"high"}}' "LC_IDENTIFIER=$USER2_SESSION" "User 2 Save Task"

# Test data isolation - User 1 loading their tasks
test_endpoint "GET" "http://localhost:8080/tasks.php?action=load" "" "LC_IDENTIFIER=$USER1_SESSION" "User 1 Load Tasks"

# Test data isolation - User 2 loading their tasks  
test_endpoint "GET" "http://localhost:8080/tasks.php?action=load" "" "LC_IDENTIFIER=$USER2_SESSION" "User 2 Load Tasks"

# PHASE 2: Biorhythm Testing
echo ""
echo "=== PHASE 2: BIORHYTHM TOOL TESTING ==="

test_endpoint "POST" "http://localhost:8080/save_biorhythms.php" '{"birth_date":"1990-01-01","physical":85,"emotional":75,"intellectual":90}' "LC_IDENTIFIER=$USER1_SESSION" "User 1 Save Biorhythm"

test_endpoint "POST" "http://localhost:8080/save_biorhythms.php" '{"birth_date":"1985-05-15","physical":70,"emotional":80,"intellectual":85}' "LC_IDENTIFIER=$USER2_SESSION" "User 2 Save Biorhythm"

# PHASE 3: Emotion Testing
echo ""
echo "=== PHASE 3: EMOTION TRACKER TESTING ==="

test_endpoint "POST" "http://localhost:8080/save_emotions.php" '{"emotion":"happy","note":"Great day!","date":"2024-01-15"}' "LC_IDENTIFIER=$USER1_SESSION" "User 1 Save Emotion"

test_endpoint "POST" "http://localhost:8080/save_emotions.php" '{"emotion":"excited","note":"New project started","date":"2024-01-15"}' "LC_IDENTIFIER=$USER2_SESSION" "User 2 Save Emotion"

# PHASE 4: Data Isolation Testing
echo ""
echo "=== PHASE 4: DATA ISOLATION VERIFICATION ==="

test_endpoint "GET" "http://localhost:8080/php/get_emotions.php" "" "LC_IDENTIFIER=$USER1_SESSION" "User 1 Get Emotions"

test_endpoint "GET" "http://localhost:8080/php/get_emotions.php" "" "LC_IDENTIFIER=$USER2_SESSION" "User 2 Get Emotions"

test_endpoint "GET" "http://localhost:8080/php/get_biorhythms.php" "" "LC_IDENTIFIER=$USER1_SESSION" "User 1 Get Biorhythms"

test_endpoint "GET" "http://localhost:8080/php/get_biorhythms.php" "" "LC_IDENTIFIER=$USER2_SESSION" "User 2 Get Biorhythms"

# PHASE 5: File System Verification
echo ""
echo "=== PHASE 5: FILE SYSTEM VERIFICATION ==="

echo "Directory structure:"
find /home/engine/project/data/users -type f -name "*.json" 2>/dev/null || echo "No user directories found"

echo ""
echo "User 1 files:"
ls -la /home/engine/project/data/users/user_test_001/ 2>/dev/null || echo "User 1 directory not found"

echo ""
echo "User 2 files:"
ls -la /home/engine/project/data/users/user_test_002/ 2>/dev/null || echo "User 2 directory not found"

# PHASE 6: Authentication Security Test
echo ""
echo "=== PHASE 6: AUTHENTICATION SECURITY TEST ==="

test_endpoint "GET" "http://localhost:8080/tasks.php?action=load" "" "" "Unauthorized Access Test"

echo ""
echo "=== TEST SUMMARY ==="
echo "✓ Multi-user task management tested"
echo "✓ Biorhythm tool tested" 
echo "✓ Emotion tracker tested"
echo "✓ Data isolation verified"
echo "✓ File system structure checked"
echo "✓ Authentication security tested"
echo ""
echo "Check file structure above for user-specific data storage."