<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json; charset=utf-8');

const DB_HOST = '127.0.0.1';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'recipe_realm';

function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function ensure_database(): void
{
    $serverConn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    $serverConn->set_charset('utf8mb4');

    $serverConn->query('CREATE DATABASE IF NOT EXISTS ' . DB_NAME . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $serverConn->select_db(DB_NAME);

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

    $indexCheck = $serverConn->prepare('SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1');
    $dbName = DB_NAME;

    $tableName = 'recipes';
    $indexName = 'idx_recipes_user_id';
    $indexCheck->bind_param('sss', $dbName, $tableName, $indexName);
    $indexCheck->execute();
    $indexResult = $indexCheck->get_result();
    if ($indexResult->num_rows === 0) {
        $serverConn->query('CREATE INDEX idx_recipes_user_id ON recipes(user_id)');
    }

    $tableName = 'messages';
    $indexName = 'idx_messages_email';
    $indexCheck->bind_param('sss', $dbName, $tableName, $indexName);
    $indexCheck->execute();
    $indexResult = $indexCheck->get_result();
    if ($indexResult->num_rows === 0) {
        $serverConn->query('CREATE INDEX idx_messages_email ON messages(email)');
    }

    $indexCheck->close();
    $serverConn->close();
}

function db_connect(): mysqli
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
    return $conn;
}

function to_list(string $text): array
{
    $parts = preg_split('/\r\n|\r|\n/', trim($text)) ?: [];
    return array_values(array_filter(array_map('trim', $parts), static fn($line) => $line !== ''));
}

function map_recipe(array $row): array
{
    return [
        'id' => (int)$row['id'],
        'title' => (string)$row['title'],
        'category' => 'User Recipe',
        'description' => 'Shared by ' . ((string)$row['username'] ?: 'community member'),
        'time' => '30 mins',
        'servings' => '4',
        'difficulty' => 'Easy',
        'image' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?q=80&w=800&auto=format&fit=crop',
        'ingredients' => to_list((string)$row['ingredients']),
        'instructions' => to_list((string)$row['instructions']),
        'featured' => false,
        'trending' => false,
        'userCreated' => true,
    ];
}

function get_or_create_user_id(mysqli $conn, string $email): int
{
    $email = strtolower(trim($email));

    $findByEmail = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $findByEmail->bind_param('s', $email);
    $findByEmail->execute();
    $result = $findByEmail->get_result();

    if ($result->num_rows > 0) {
        $userId = (int)$result->fetch_assoc()['id'];
        $findByEmail->close();
        return $userId;
    }
    $findByEmail->close();

    $rawBase = strstr($email, '@', true) ?: 'user';
    $baseUsername = preg_replace('/[^a-z0-9_]/i', '', $rawBase) ?: 'user';

    $username = $baseUsername;
    $suffix = 1;

    $checkName = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    while (true) {
        $checkName->bind_param('s', $username);
        $checkName->execute();
        $nameResult = $checkName->get_result();

        if ($nameResult->num_rows === 0) {
            break;
        }

        $suffix++;
        $username = $baseUsername . '_' . $suffix;
    }
    $checkName->close();

    $passwordHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);

    $insertUser = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
    $insertUser->bind_param('sss', $username, $email, $passwordHash);
    $insertUser->execute();
    $newId = $insertUser->insert_id;
    $insertUser->close();

    return (int)$newId;
}

try {
    ensure_database();
    $conn = db_connect();

    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'recipes') {
        $query = 'SELECT r.id, r.title, r.ingredients, r.instructions, u.username FROM recipes r INNER JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC';
        $result = $conn->query($query);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = map_recipe($row);
        }

        json_response(['ok' => true, 'recipes' => $rows]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_recipe') {
        $payload = json_decode(file_get_contents('php://input') ?: '', true);
        if (!is_array($payload)) {
            json_response(['ok' => false, 'error' => 'Invalid JSON payload.'], 400);
        }

        $title = trim((string)($payload['title'] ?? ''));
        $email = trim((string)($payload['email'] ?? ''));
        $ingredients = $payload['ingredients'] ?? [];
        $instructions = $payload['instructions'] ?? [];

        if ($title === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            json_response(['ok' => false, 'error' => 'Title and valid email are required.'], 422);
        }

        if (!is_array($ingredients) || count($ingredients) === 0 || !is_array($instructions) || count($instructions) === 0) {
            json_response(['ok' => false, 'error' => 'Ingredients and instructions are required.'], 422);
        }

        $ingredientsText = implode("\n", array_values(array_filter(array_map('trim', $ingredients), static fn($line) => $line !== '')));
        $instructionsText = implode("\n", array_values(array_filter(array_map('trim', $instructions), static fn($line) => $line !== '')));

        if ($ingredientsText === '' || $instructionsText === '') {
            json_response(['ok' => false, 'error' => 'Ingredients and instructions cannot be empty.'], 422);
        }

        $userId = get_or_create_user_id($conn, $email);

        $insert = $conn->prepare('INSERT INTO recipes (title, ingredients, instructions, user_id) VALUES (?, ?, ?, ?)');
        $insert->bind_param('sssi', $title, $ingredientsText, $instructionsText, $userId);
        $insert->execute();
        $newRecipeId = (int)$insert->insert_id;
        $insert->close();

        $select = $conn->prepare('SELECT r.id, r.title, r.ingredients, r.instructions, u.username FROM recipes r INNER JOIN users u ON r.user_id = u.id WHERE r.id = ? LIMIT 1');
        $select->bind_param('i', $newRecipeId);
        $select->execute();
        $recipeResult = $select->get_result();
        $recipeRow = $recipeResult->fetch_assoc();
        $select->close();

        if (!$recipeRow) {
            json_response(['ok' => false, 'error' => 'Recipe saved but could not be reloaded.'], 500);
        }

        json_response(['ok' => true, 'recipe' => map_recipe($recipeRow)], 201);
    }

    json_response(['ok' => false, 'error' => 'Route not found.'], 404);
} catch (Throwable $e) {
    json_response(['ok' => false, 'error' => $e->getMessage()], 500);
}
