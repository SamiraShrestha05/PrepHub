<?php
// Load environment variables
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$database = $_ENV['DB_DATABASE'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

echo "🔍 Testing connection to Supabase...\n\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "Password: " . str_repeat('*', strlen($password)) . "\n\n";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$database;sslmode=require";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ SUCCESS! Connected to Supabase!\n\n";
    
    // Get PostgreSQL version
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "PostgreSQL Version: $version\n";
    
    // List databases
    $stmt = $pdo->query("SELECT datname FROM pg_database WHERE datistemplate = false");
    echo "\nAvailable databases:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - " . $row['datname'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ CONNECTION FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n";
    
    // Helpful troubleshooting
    echo "\n🔧 Troubleshooting tips:\n";
    if (strpos($e->getMessage(), 'could not translate host name')) {
        echo "  • Check if hostname is correct\n";
    } elseif (strpos($e->getMessage(), 'Connection refused')) {
        echo "  • Check if port 5432 is correct\n";
        echo "  • Supabase might be blocking your IP\n";
    } elseif (strpos($e->getMessage(), 'password authentication failed')) {
        echo "  • Check your password\n";
        echo "  • Username should be 'postgres'\n";
    } elseif (strpos($e->getMessage(), 'SSL')) {
        echo "  • Add 'sslmode=require' to connection\n";
    }
}
