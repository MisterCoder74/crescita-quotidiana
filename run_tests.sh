#!/bin/bash

echo "========================================="
echo "CRESCITA QUOTIDIANA - MULTI-USER TEST"
echo "========================================="

# Clean start
echo ""
echo "=== CLEANING UP PREVIOUS TEST DATA ==="
rm -rf /home/engine/project/data/users/user_test_*
rm -f /tmp/*.json
echo "✓ Cleaned up previous test data"

# Ensure users directory exists
mkdir -p /home/engine/project/data/users
echo "✓ Users directory ready"

echo ""
echo "=== RUNNING COMPREHENSIVE PHP TEST ==="
php -f /home/engine/project/multi_user_test.php

echo ""
echo "=== TEST COMPLETED ==="
echo "Check the results above for any issues found."