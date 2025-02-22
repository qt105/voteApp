<?php
session_start();

include __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../templates/header.html.php";
require_once __DIR__ . "/../vendor/autoload.php";

use App\VoteApp\ClassUser;

if (empty($_POST)) {
    require_once "../templates/formUser.html.php";
} else {
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['email']) || empty($_POST['birth_date'])) {
        echo "Erreur : Tous les champs sont obligatoires.";
        require_once "../templates/formUser.html.php";
        exit;
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo "Erreur : Adresse email invalide.";
        require_once "../templates/formUser.html.php";
        exit;
    }

    $user = new ClassUser(
        username: $_POST['username'], 
        password: $_POST['password']
    );

    $user->setFirstName($_POST['firstName'])
         ->setLastName($_POST['lastName'])
         ->setRole("user")
         ->setEmail($_POST['email'])
         ->setBirthDate($_POST['birth_date']);

    $stmtUsername = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmtUsername->execute(['username' => $user->getUsername()]);
    if ($stmtUsername->fetchColumn()) {
        echo "Erreur: Nom d'utilisateur '" . htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') . "' déjà utilisé.";
        require_once "../templates/formUser.html.php";
        exit;
    }

    $stmtEmail = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmtEmail->execute(['email' => $user->getEmail()]);
    if ($stmtEmail->fetchColumn()) {
        echo "Erreur: L'email '" . htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8') . "' est déjà utilisé.";
        require_once "../templates/formUser.html.php";
        exit;
    }

    $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, firstName, lastName, email, role, birth_date) VALUES (:username, :password, :firstName, :lastName, :email, :role, :birth_date)");
        $stmt->execute([
            'username' => $user->getUsername(),
            'password' => $hashedPassword,
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'birth_date' => $user->getBirthDate()
        ]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        header("Location: profile.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de l'inscription : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        require_once "../templates/formUser.html.php";
        exit;
    }
}