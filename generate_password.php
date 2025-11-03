<?php
/**
 * Password Hash Generator
 * 
 * This script generates a password hash for the Dakoii admin user
 * Access via browser: http://localhost/corres/generate_password.php
 */

$password = 'dakoii';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password Hash Generator\n";
echo "=======================\n\n";
echo "Plain Password: " . $password . "\n";
echo "Hashed Password: " . $hash . "\n\n";
echo "Copy the hashed password above and use it in the SQL file or seeder.\n";

// If running in browser, format output nicely
if (php_sapi_name() !== 'cli') {
    echo "<script>document.body.innerHTML = '<pre>' + document.body.innerHTML + '</pre>';</script>";
}

