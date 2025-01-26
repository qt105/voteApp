<?php
session_start();
require_once __DIR__ . "/../templates/header.html.php";
require_once __DIR__ . "/../config/database.php";

// Fetch active consultations
$stmt = $pdo->query("
    SELECT c.*, u.username as creator_name, 
           (SELECT COUNT(*) FROM votes v WHERE v.consultation_id = c.id) as vote_count
    FROM consultations c
    JOIN users u ON c.creator_id = u.id
    WHERE c.status = 'active'
    AND NOW() BETWEEN c.start_date AND c.end_date
    ORDER BY c.created_at DESC
    LIMIT 10
");
$activeConsultations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Vote Condorcet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-3xl font-bold mb-4">Bienvenue sur notre système de vote Condorcet</h1>
            <p class="text-gray-600">
                Le système de vote Condorcet permet de classer les options par ordre de préférence,
                offrant ainsi un résultat plus représentatif des souhaits des votants.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Consultations actives</h2>
                <?php if ($activeConsultations): ?>
                    <div class="space-y-4">
                        <?php foreach ($activeConsultations as $consultation): ?>
                            <div class="border p-4 rounded-lg">
                                <h3 class="font-bold"><?= htmlspecialchars($consultation->title) ?></h3>
                                <p class="text-sm text-gray-600">
                                    Créé par: <?= htmlspecialchars($consultation->creator_name) ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    Votes: <?= $consultation->vote_count ?>
                                </p>
                                <div class="mt-2">
                                    <a href="consultation.php?id=<?= $consultation->id ?>" 
                                       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                        Voir détails
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Aucune consultation active pour le moment.</p>
                <?php endif; ?>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Actions</h2>
                    <div class="space-y-4">
                        <a href="createSelection.php" 
                           class="block bg-green-500 text-white text-center px-4 py-2 rounded hover:bg-green-600">
                            Créer une nouvelle consultation
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
