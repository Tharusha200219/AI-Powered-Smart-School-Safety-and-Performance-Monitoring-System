<?php
/**
 * Database Connection Test Script
 * Tests MySQL database connectivity using credentials from .env file
 */

// Load environment variables
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "DATABASE CONNECTION TEST\n";
echo "========================================\n\n";

// Get database configuration
$host = env('DB_HOST', '127.0.0.1');
$port = env('DB_PORT', '3306');
$database = env('DB_DATABASE', 'safe_learn_hub');
$username = env('DB_USERNAME', 'root');

echo "Configuration:\n";
echo "  Host: $host\n";
echo "  Port: $port\n";
echo "  Database: $database\n";
echo "  Username: $username\n\n";

// Test 1: PDO Connection
echo "Test 1: PDO Connection Test\n";
echo "----------------------------\n";
try {
    $password = env('DB_PASSWORD', '');
    $dsn = "mysql:host=$host;port=$port;dbname=$database";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ PDO Connection: SUCCESS\n";
    echo "  Server Version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
} catch (PDOException $e) {
    echo "✗ PDO Connection: FAILED\n";
    echo "  Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Laravel DB Connection
echo "Test 2: Laravel DB Connection Test\n";
echo "-----------------------------------\n";
try {
    DB::connection()->getPdo();
    echo "✓ Laravel DB Connection: SUCCESS\n";
    
    // Get database name
    $dbName = DB::connection()->getDatabaseName();
    echo "  Connected to database: $dbName\n\n";
} catch (Exception $e) {
    echo "✗ Laravel DB Connection: FAILED\n";
    echo "  Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 3: Query Test
echo "Test 3: Query Test\n";
echo "------------------\n";
try {
    $result = DB::select('SELECT VERSION() as version, DATABASE() as database, NOW() as current_time');
    echo "✓ Query Execution: SUCCESS\n";
    echo "  MySQL Version: " . $result[0]->version . "\n";
    echo "  Current Database: " . $result[0]->database . "\n";
    echo "  Current Time: " . $result[0]->current_time . "\n\n";
} catch (Exception $e) {
    echo "✗ Query Execution: FAILED\n";
    echo "  Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 4: Tables Check
echo "Test 4: Tables Check\n";
echo "--------------------\n";
try {
    $tables = DB::select('SHOW TABLES');
    echo "✓ Tables Found: " . count($tables) . " tables\n";
    
    if (count($tables) > 0) {
        echo "\n  Sample tables:\n";
        $limit = min(10, count($tables));
        for ($i = 0; $i < $limit; $i++) {
            $tableArray = (array)$tables[$i];
            $tableName = array_values($tableArray)[0];
            echo "    - $tableName\n";
        }
        if (count($tables) > 10) {
            echo "    ... and " . (count($tables) - 10) . " more\n";
        }
    }
    echo "\n";
} catch (Exception $e) {
    echo "✗ Tables Check: FAILED\n";
    echo "  Error: " . $e->getMessage() . "\n\n";
}

echo "========================================\n";
echo "✓ ALL TESTS PASSED\n";
echo "Database connection is working properly!\n";
echo "========================================\n";
