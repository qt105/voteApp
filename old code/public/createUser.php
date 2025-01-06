<?php
session_start();

include __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../templates/header.html.php";
require_once __DIR__ . "/../vendor/autoload.php";
use App\VoteApp\ClassUser;

if (empty($_POST)) {
    require_once "../templates/formUser.html.php";
} else {
    $user = new ClassUser(
        username: $_POST['username'], 
        password: $_POST['password']);

    $user->setFirstName($_POST['firstName'])
         ->setLastName($_POST['lastName'])
         ->setRole("user")
         ->setEmail($_POST['email']);

    $_SESSION['user'] = serialize($user);


    $stmtUsername = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmtUsername->execute(['username' => $user->getUsername()]);
    $usernameExists = $stmtUsername->fetchColumn();

    if ($usernameExists) {
        echo "Erreur: Nom d'utilisateur '" . htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') . "' déjà utilisé. Merci d'essayer un autre nom d'utilisateur.";
        require_once "../templates/formUser.html.php";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO users (username, password, firstName, lastName, email, role) VALUES (:username, :password, :firstName, :lastName, :email, :role)");
    

    $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);

    $userData = [
        'username' => $user->getUsername(),
        'password' => $hashedPassword,
        'firstName' => $user->getFirstName(),
        'lastName' => $user->getLastName(),
        'email' => $user->getEmail(),
        'role' => $user->getRole()
    ];

    $stmt->execute($userData);

    require_once "../templates/profile.html.php";
}