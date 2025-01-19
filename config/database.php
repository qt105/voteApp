<?php
/* Database Host */
define("DB_HOST", "localhost");
/* Database Port */
define("DB_PORT", "3306");
/* Database Name */
define("DB_NAME", "voteApp_DB");
/* Database User */
define("DB_USER", "root");
/* Database User Password */
define("DB_PASS", "root");

try {
    $pdo = new PDO(
        "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST . ";port=" . DB_PORT,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    die("Cannot connect" . $e->getMessage());
}
