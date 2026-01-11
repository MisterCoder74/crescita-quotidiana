#!/bin/bash

echo "========================================="
echo "FRONTEND INTEGRATION TEST"
echo "========================================="

# Test frontend pages with session
echo ""
echo "=== TESTING FRONTEND PAGES ==="

# Test dashboard
echo "Testing dashboard.php..."
response=$(curl -s "http://localhost:8080/dashboard.php" -H "Cookie: LC_IDENTIFIER=test123")
if echo "$response" | grep -q "Crescita Quotidiana - Dashboard"; then
    echo "✓ Dashboard loads correctly"
else
    echo "✗ Dashboard loading issue"
fi

# Test planner
echo "Testing planner.html..."
response=$(curl -s "http://localhost:8080/planner.html" -H "Cookie: LC_IDENTIFIER=test123")
if echo "$response" | grep -q "Mega Planner"; then
    echo "✓ Planner loads correctly"
else
    echo "✗ Planner loading issue"
fi

# Test biorhythm page
echo "Testing bioritmi.html..."
response=$(curl -s "http://localhost:8080/bioritmi.html" -H "Cookie: LC_IDENTIFIER=test123")
if echo "$response" | grep -q "Bioritmi"; then
    echo "✓ Biorhythm page loads correctly"
else
    echo "✗ Biorhythm page loading issue"
fi

# Test emotion tracker
echo "Testing emotracker.html..."
response=$(curl -s "http://localhost:8080/emotracker.html" -H "Cookie: LC_IDENTIFIER=test123")
if echo "$response" | grep -q "Emotion Tracker"; then
    echo "✓ Emotion tracker loads correctly"
else
    echo "✗ Emotion tracker loading issue"
fi

echo ""
echo "=== CHECKING API INTEGRATION IN FRONTEND ==="

# Check if planner.html properly calls user-specific APIs
echo "Checking planner.html API calls:"
if grep -q "tasks.php?action=load" /home/engine/project/planner.html; then
    echo "✓ Planner calls tasks.php correctly"
else
    echo "✗ Planner missing tasks.php calls"
fi

if grep -q "php/get_emotions.php" /home/engine/project/planner.html; then
    echo "✓ Planner calls get_emotions.php correctly"
else
    echo "✗ Planner missing get_emotions.php calls"
fi

if grep -q "php/get_biorhythms.php" /home/engine/project/planner.html; then
    echo "✓ Planner calls get_biorhythms.php correctly"
else
    echo "✗ Planner missing get_biorhythms.php calls"
fi

echo ""
echo "=== SECURITY VERIFICATION ==="

# Test unauthorized access to frontend pages
echo "Testing unauthorized access to dashboard.php..."
response=$(curl -s "http://localhost:8080/dashboard.php")
if echo "$response" | grep -q "index.html"; then
    echo "✓ Unauthorized access properly redirected"
else
    echo "✗ Security issue: unauthorized access allowed"
fi

echo ""
echo "=== FINAL VERIFICATION ==="

# Show final file structure
echo "Final user data structure:"
find /home/engine/project/data/users -name "*.json" -exec basename {} \; | sort | uniq -c

echo ""
echo "Test completed!"