<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'voteApp_DB');
define('DB_USER', 'root');
define('DB_PASS', '1328');

try {
    $pdo = new PDO(
        'mysql:dbname='.DB_NAME.';host='.DB_HOST.';port='.DB_PORT,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    echo "connection successful!";
} catch(PDOException $e) {
    die('Cannot connect' . $e->getMessage());
} 