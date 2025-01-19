<?php
session_start();

require_once __DIR__ . "/../templates/header.html.php";

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginUser.php");
    exit;
}

// Include the database connection
include __DIR__ . "/../config/database.php";

// Fetch the user's information from the database
$stmt = $pdo->prepare("SELECT username, firstName, lastName, email, role FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If no user is found, log out and redirect to login
if (!$user) {
    session_destroy();
    header("Location: loginUser.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil utilisateur</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Bienvenue, <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName'], ENT_QUOTES, 'UTF-8') ?>!</h1>
    </header>

    <main>
        <section>
            <h2>Informations de votre profil</h2>
            <ul>
                <li><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Prénom :</strong> <?= htmlspecialchars($user['firstName'], ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Nom :</strong> <?= htmlspecialchars($user['lastName'], ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Email :</strong> <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Rôle :</strong> <?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?></li>
            </ul>
        </section>

        <section>
            <form action="logout.php" method="POST">
                <button type="submit">Se déconnecter</button>
            </form>
        </section>
    </main>
</body>
</html>
