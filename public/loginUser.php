<?php
session_start();

include __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../templates/header.html.php";
require_once __DIR__ . "/../vendor/autoload.php";

use App\VoteApp\ClassUser;

if (empty($_POST)) {
    require_once "../templates/loginFormUser.html.php";
} else {

    $user = new ClassUser(
        username: $_POST['username'], 
        password: $_POST['password']);

        $stmtUsername = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmtUsername->execute(['username' => $user->getUsername()]);
        $usernameExists = $stmtUsername->fetchColumn();
    
        if (!$usernameExists) {
            echo "Erreur: nom d'utilisateur incorrect '" . htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') . "' n'existe pas";
            require_once "../templates/loginFormUser.html.php";
            exit;
        }
    
        $stmtPassword = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmtPassword->execute(['username' => $user->getPassword()]);
        $password = $stmtPassword;
    
        if () {
            echo "Erreur: nom d'utilisateur incorrect '" . htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') . "' n'existe pas";
            require_once "../templates/loginFormUser.html.php";
            exit;
        }
}