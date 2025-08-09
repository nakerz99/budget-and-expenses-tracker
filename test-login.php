<?php
/**
 * Test Login Page - Bypasses router to test authentication
 */

session_start();

echo "<h1>Test Login Page</h1>";
echo "<p>This page bypasses the router to test authentication.</p>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Actions:</h2>";
echo "<a href='test-login.php?clear=1'>Clear Session</a><br>";
echo "<a href='login.php'>Go to Real Login</a><br>";
echo "<a href='index.php'>Go to Dashboard</a><br>";

if (isset($_GET['clear'])) {
    session_destroy();
    session_start();
    echo "<p>Session cleared!</p>";
}
?>
