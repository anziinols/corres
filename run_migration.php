<?php

/**
 * Migration and Seeder Runner Script
 * 
 * This script runs the database migration and seeder for the Dakoii Admin Portal
 * 
 * Usage: Access this file via browser at http://localhost/corres/run_migration.php
 * or run via command line: php run_migration.php
 */

// Load CodeIgniter
require_once __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/Boot.php';

use CodeIgniter\Boot;

// Boot the application
$app = Boot::bootWeb($paths);

// Get database instance
$db = \Config\Database::connect();

echo "=== Dakoii Admin Portal - Database Setup ===\n\n";

// Check if migrations table exists
$tableExists = $db->tableExists('migrations');
if (!$tableExists) {
    echo "Creating migrations table...\n";
}

// Run migrations
echo "Running migrations...\n";
try {
    $migrate = \Config\Services::migrations();
    $migrate->latest();
    echo "✓ Migrations completed successfully!\n\n";
} catch (\Exception $e) {
    echo "✗ Migration error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Check if dakoii_users table was created
if ($db->tableExists('dakoii_users')) {
    echo "✓ Table 'dakoii_users' created successfully!\n\n";
    
    // Check if user already exists
    $builder = $db->table('dakoii_users');
    $existingUser = $builder->where('username', 'fkenny')->get()->getRow();
    
    if ($existingUser) {
        echo "⚠ User 'fkenny' already exists. Skipping seeder.\n\n";
    } else {
        // Run seeder
        echo "Running seeder...\n";
        try {
            $seeder = \Config\Database::seeder();
            $seeder->call('DakoiiUserSeeder');
            echo "✓ Seeder completed successfully!\n\n";
            echo "Default user created:\n";
            echo "  Name: Free Kenny\n";
            echo "  Username: fkenny\n";
            echo "  Password: dakoii\n\n";
        } catch (\Exception $e) {
            echo "✗ Seeder error: " . $e->getMessage() . "\n\n";
            exit(1);
        }
    }
} else {
    echo "✗ Table 'dakoii_users' was not created!\n\n";
    exit(1);
}

echo "=== Setup Complete! ===\n";
echo "You can now access the Dakoii Admin Portal at:\n";
echo "http://localhost/corres/dakoii\n\n";
echo "Login credentials:\n";
echo "  Username: fkenny\n";
echo "  Password: dakoii\n\n";

// If running in browser, format output nicely
if (php_sapi_name() !== 'cli') {
    echo "<script>document.body.innerHTML = '<pre>' + document.body.innerHTML + '</pre>';</script>";
}

