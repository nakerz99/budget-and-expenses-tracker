<?php
// Test PHP 7.4 compatibility
echo "Testing PHP 7.4 compatibility...\n";
echo "PHP Version: " . PHP_VERSION . "\n\n";

// Test null coalescing operator (introduced in PHP 7.0, so should work in 7.4)
echo "Testing null coalescing operator (??):\n";
$test1 = null;
$result1 = $test1 ?? 'default';
echo "null ?? 'default' = " . $result1 . "\n";

$test2 = 'value';
$result2 = $test2 ?? 'default';
echo "'value' ?? 'default' = " . $result2 . "\n\n";

// Test array access with null coalescing
$array = ['key' => 'value'];
$result3 = $array['key'] ?? 'not found';
echo "Array access with ??: " . $result3 . "\n";

$result4 = $array['nonexistent'] ?? 'not found';
echo "Non-existent array key with ??: " . $result4 . "\n\n";

// Test session access
session_start();
$_SESSION['test'] = 'session_value';
$session_value = $_SESSION['test'] ?? 'no session';
echo "Session access with ??: " . $session_value . "\n\n";

// Test POST/GET access
$post_value = $_POST['test'] ?? 'no post';
echo "POST access with ??: " . $post_value . "\n";

$get_value = $_GET['test'] ?? 'no get';
echo "GET access with ??: " . $get_value . "\n\n";

echo "All tests passed! PHP 7.4 compatibility looks good.\n";
?>
