<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: text/plain; charset=utf-8');

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbName = 'recipe_realm';

try {
    $serverConn = new mysqli($host, $user, $pass);
    $serverConn->set_charset('utf8mb4');

    $serverConn->query("CREATE DATABASE IF NOT EXISTS {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $serverConn->select_db($dbName);

    $serverConn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $serverConn->query("CREATE TABLE IF NOT EXISTS recipes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        ingredients TEXT NOT NULL,
        instructions TEXT NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $serverConn->query("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $indexCheck = $serverConn->prepare("SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1");

    $table = 'recipes';
    $index = 'idx_recipes_user_id';
    $indexCheck->bind_param('sss', $dbName, $table, $index);
    $indexCheck->execute();
    $indexResult = $indexCheck->get_result();
    if ($indexResult->num_rows === 0) {
        $serverConn->query('CREATE INDEX idx_recipes_user_id ON recipes(user_id)');
    }

    $table = 'messages';
    $index = 'idx_messages_email';
    $indexCheck->bind_param('sss', $dbName, $table, $index);
    $indexCheck->execute();
    $indexResult = $indexCheck->get_result();
    if ($indexResult->num_rows === 0) {
        $serverConn->query('CREATE INDEX idx_messages_email ON messages(email)');
    }

    $indexCheck->close();
    $serverConn->close();

    echo "Database setup completed successfully.\n";
    echo "Database: recipe_realm\n";
    echo "Tables: users, recipes, messages\n";
    echo "Indexes: idx_recipes_user_id, idx_messages_email\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Database setup failed: " . $e->getMessage() . "\n";
}
