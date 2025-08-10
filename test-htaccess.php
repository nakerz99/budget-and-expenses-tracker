<?php
/**
 * Test script to check .htaccess and mod_rewrite functionality
 * Access this file to see server configuration details
 */

echo "<h1>🔧 .htaccess & mod_rewrite Test</h1>";

// Check if mod_rewrite is enabled
echo "<h2>📋 Server Information</h2>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Check if .htaccess is being processed
echo "<h2>🔍 .htaccess Test</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color: green;'>✅ mod_rewrite is ENABLED</p>";
    } else {
        echo "<p style='color: red;'>❌ mod_rewrite is DISABLED</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Cannot check mod_rewrite status (function not available)</p>";
}

// Test URL rewriting
echo "<h2>🧪 URL Rewriting Test</h2>";
echo "<p>Testing if clean URLs work:</p>";

$testUrls = [
    '/income' => 'Should redirect to pages/income.php',
    '/expenses' => 'Should redirect to pages/expenses.php',
    '/dashboard' => 'Should redirect to index.php',
    '/login' => 'Should redirect to login.php'
];

echo "<ul>";
foreach ($testUrls as $url => $description) {
    $fullUrl = 'https://' . $_SERVER['HTTP_HOST'] . $url;
    echo "<li><a href='{$fullUrl}' target='_blank'>{$url}</a> - {$description}</li>";
}

echo "</ul>";

// Check if .htaccess file exists
echo "<h2>📁 File Check</h2>";
$htaccessPath = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo "<p style='color: green;'>✅ .htaccess file exists at: {$htaccessPath}</p>";
    
    // Check file permissions
    $perms = fileperms($htaccessPath);
    $perms = substr(sprintf('%o', $perms), -4);
    echo "<p><strong>File Permissions:</strong> {$perms}</p>";
    
    // Show first few lines
    $content = file_get_contents($htaccessPath);
    $lines = explode("\n", $content);
    echo "<p><strong>First 5 lines of .htaccess:</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    for ($i = 0; $i < min(5, count($lines)); $i++) {
        echo htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ .htaccess file NOT FOUND at: {$htaccessPath}</p>";
}

// Alternative solutions
echo "<h2>🛠️ Alternative Solutions</h2>";
echo "<p>If clean URLs don't work, you can use these direct links:</p>";
echo "<ul>";
echo "<li><a href='/pages/income.php'>/pages/income.php</a> - Direct access to income page</li>";
echo "<li><a href='/pages/expenses.php'>/pages/expenses.php</a> - Direct access to expenses page</li>";
echo "<li><a href='/index.php'>/index.php</a> - Direct access to dashboard</li>";
echo "</ul>";

// Troubleshooting steps
echo "<h2>🔧 Troubleshooting Steps</h2>";
echo "<ol>";
echo "<li><strong>Check if .htaccess is uploaded:</strong> Make sure the .htaccess file exists in your public_html directory</li>";
echo "<li><strong>Verify mod_rewrite:</strong> Contact Hostinger support to enable mod_rewrite if it's disabled</li>";
echo "<li><strong>Check file permissions:</strong> .htaccess should have 644 permissions</li>";
echo "<li><strong>Clear browser cache:</strong> Sometimes browsers cache 404 errors</li>";
echo "<li><strong>Use direct PHP links:</strong> As a temporary solution, use /pages/income.php instead of /income</li>";
echo "</ol>";

// Test current working URLs
echo "<h2>✅ Working URLs Test</h2>";
echo "<p>These should work regardless of .htaccess:</p>";
echo "<ul>";
echo "<li><a href='/login.php'>/login.php</a> - Login page</li>";
echo "<li><a href='/index.php'>/index.php</a> - Dashboard</li>";
echo "<li><a href='/pages/income.php'>/pages/income.php</a> - Income page</li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>If you're still having issues, contact Hostinger support and ask them to:</em></p>";
echo "<ul>";
echo "<li>Enable mod_rewrite module</li>";
echo "<li>Allow .htaccess processing</li>";
echo "<li>Check if AllowOverride is set to All</li>";
echo "</ul>";
?>
