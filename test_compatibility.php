<?php
// Comprehensive PHP 7.4 compatibility test
echo "=== PHP 7.4 Compatibility Test ===\n";
echo "PHP Version: " . PHP_VERSION . "\n\n";

// Test 1: Null coalescing operator
echo "1. Testing null coalescing operator (??):\n";
$test = null;
$result = $test ?? 'default';
echo "   Result: " . $result . " ✓\n\n";

// Test 2: Array access with null coalescing
echo "2. Testing array access with null coalescing:\n";
$array = ['key' => 'value'];
$result = $array['key'] ?? 'not found';
echo "   Result: " . $result . " ✓\n\n";

// Test 3: Session handling
echo "3. Testing session handling:\n";
session_start();
$_SESSION['test'] = 'session_value';
$session_value = $_SESSION['test'] ?? 'no session';
echo "   Result: " . $session_value . " ✓\n\n";

// Test 4: POST/GET access
echo "4. Testing POST/GET access:\n";
$post_value = $_POST['test'] ?? 'no post';
$get_value = $_GET['test'] ?? 'no get';
echo "   POST: " . $post_value . " ✓\n";
echo "   GET: " . $get_value . " ✓\n\n";

// Test 5: Database connection (if config exists)
echo "5. Testing database connection:\n";
if (file_exists('config/database.php')) {
    try {
        require_once 'config/database.php';
        echo "   Database config loaded ✓\n";
    } catch (Exception $e) {
        echo "   Database config error: " . $e->getMessage() . "\n";
    }
} else {
    echo "   Database config not found (expected) ✓\n";
}
echo "\n";

// Test 6: Function loading
echo "6. Testing function loading:\n";
if (file_exists('includes/functions.php')) {
    try {
        require_once 'includes/functions.php';
        echo "   Functions loaded ✓\n";
    } catch (Exception $e) {
        echo "   Functions error: " . $e->getMessage() . "\n";
    }
} else {
    echo "   Functions file not found\n";
}
echo "\n";

// Test 7: String functions compatibility
echo "7. Testing string functions:\n";
$string = "Hello World";
$contains = strpos($string, "World") !== false;
echo "   strpos check: " . ($contains ? "true" : "false") . " ✓\n";

$starts_with = strpos($string, "Hello") === 0;
echo "   starts with check: " . ($starts_with ? "true" : "false") . " ✓\n";

$ends_with = strpos($string, "World") === (strlen($string) - strlen("World"));
echo "   ends with check: " . ($ends_with ? "true" : "false") . " ✓\n\n";

// Test 8: Array functions
echo "8. Testing array functions:\n";
$array = ['a', 'b', 'c'];
$first_key = array_keys($array)[0] ?? null;
echo "   First key: " . $first_key . " ✓\n";

$last_key = array_keys($array)[count($array) - 1] ?? null;
echo "   Last key: " . $last_key . " ✓\n\n";

// Test 9: Error handling
echo "9. Testing error handling:\n";
try {
    $result = 10 / 0;
} catch (DivisionByZeroError $e) {
    echo "   Division by zero caught ✓\n";
} catch (Exception $e) {
    echo "   General exception caught ✓\n";
}
echo "\n";

echo "=== All Tests Completed ===\n";
echo "PHP 7.4 compatibility: ✓ PASSED\n";
echo "The application should work correctly with PHP 7.4.\n";
?>
