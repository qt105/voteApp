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
        password: $_POST['password']
    );

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $user->getUsername()]);
    $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dbUser) {
        echo "Erreur : Nom d'utilisateur ou mot de passe incorrect.";
        require_once "../templates/loginFormUser.html.php";
        exit;
    }

    if (!password_verify($user->getPassword(), $dbUser['password'])) {
        echo "Erreur : Nom d'utilisateur ou mot de passe incorrect.";
        require_once "../templates/loginFormUser.html.php";
        exit;
    }

    $_SESSION['user_id'] = $dbUser['id'];
    $_SESSION['role'] = $dbUser['role'];
    header("Location: profile.php");
    exit;
}
