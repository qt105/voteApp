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
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// If no user is found, log out and redirect to login
if (!$user) {
    session_destroy();
    header("Location: loginUser.php");
    exit;
}

// Fetch user's consultations (created)
$stmt = $pdo->prepare("
    SELECT c.*, 
           (SELECT COUNT(*) FROM votes v WHERE v.consultation_id = c.id) as vote_count
    FROM consultations c
    WHERE c.creator_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$createdConsultations = $stmt->fetchAll();

// Fetch consultations where user has voted
$stmt = $pdo->prepare("
    SELECT c.*, v.created_at as voted_at
    FROM consultations c
    JOIN votes v ON c.id = v.consultation_id
    WHERE v.voter_id = ?
    ORDER BY v.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$votedConsultations = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?= htmlspecialchars($user->username) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Mes consultations créées</h2>
                <?php if ($createdConsultations): ?>
                    <div class="space-y-4">
                        <?php foreach ($createdConsultations as $consultation): ?>
                            <div class="border p-4 rounded">
                                <h3 class="font-bold">
                                    <a href="consultation.php?id=<?= $consultation->id ?>" class="text-blue-600 hover:underline">
                                        <?= htmlspecialchars($consultation->title) ?>
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Status: <?= $consultation->status ?><br>
                                    Votes: <?= $consultation->vote_count ?><br>
                                    Créé le: <?= $consultation->created_at ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Vous n'avez pas encore créé de consultation.</p>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Mes votes</h2>
                <?php if ($votedConsultations): ?>
                    <div class="space-y-4">
                        <?php foreach ($votedConsultations as $consultation): ?>
                            <div class="border p-4 rounded">
                                <h3 class="font-bold">
                                    <a href="consultation.php?id=<?= $consultation->id ?>" class="text-blue-600 hover:underline">
                                        <?= htmlspecialchars($consultation->title) ?>
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Voté le: <?= $consultation->voted_at ?><br>
                                    <a href="results.php?id=<?= $consultation->id ?>" class="text-blue-600 hover:underline">
                                        Voir les résultats
                                    </a>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Vous n'avez pas encore participé à des consultations.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
