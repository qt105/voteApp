<?php
session_start();
require_once __DIR__ . "/../templates/header.html.php";
require_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$consultationId = $_GET['id'];

// Fetch consultation details
$stmt = $pdo->prepare("
    SELECT c.*, u.username as creator_name,
           (SELECT COUNT(*) FROM votes v WHERE v.consultation_id = c.id) as total_votes
    FROM consultations c
    JOIN users u ON c.creator_id = u.id
    WHERE c.id = ?
");
$stmt->execute([$consultationId]);
$consultation = $stmt->fetch();

if (!$consultation) {
    header("Location: index.php");
    exit;
}

// Fetch choices
$stmt = $pdo->prepare("SELECT * FROM choices WHERE consultation_id = ?");
$stmt->execute([$consultationId]);
$choices = $stmt->fetchAll();

// Calculate Condorcet winner
$matrix = [];
foreach ($choices as $choice) {
    $matrix[$choice->id] = array_fill(0, count($choices), 0);
}

// Fill the preference matrix
$stmt = $pdo->prepare("
    SELECT winner_choice_id, loser_choice_id, COUNT(*) as count
    FROM vote_details
    WHERE vote_id IN (SELECT id FROM votes WHERE consultation_id = ?)
    GROUP BY winner_choice_id, loser_choice_id
");
$stmt->execute([$consultationId]);
$preferences = $stmt->fetchAll();

foreach ($preferences as $pref) {
    $matrix[$pref->winner_choice_id][$pref->loser_choice_id] = $pref->count;
}

// Find Condorcet winner
$winner = null;
$choiceScores = [];

foreach ($choices as $choice) {
    $wins = 0;
    foreach ($choices as $opponent) {
        if ($choice->id !== $opponent->id) {
            if ($matrix[$choice->id][$opponent->id] > $matrix[$opponent->id][$choice->id]) {
                $wins++;
            }
        }
    }
    $choiceScores[$choice->id] = $wins;
    
    if ($wins === count($choices) - 1) {
        $winner = $choice;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats - <?= htmlspecialchars($consultation->title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($consultation->title) ?></h1>
            <p class="text-gray-600 mb-4">
                Créé par: <?= htmlspecialchars($consultation->creator_name) ?><br>
                Nombre total de votes: <?= $consultation->total_votes ?>
            </p>

            <?php if ($winner): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <p class="font-bold">Gagnant de Condorcet:</p>
                    <p><?= htmlspecialchars($winner->title) ?></p>
                </div>
            <?php else: ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                    <p>Pas de gagnant de Condorcet clair (paradoxe de Condorcet)</p>
                </div>
            <?php endif; ?>

            <h2 class="text-xl font-bold mb-4">Matrice des préférences</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border">
                    <thead>
                        <tr>
                            <th class="border p-2">Option</th>
                            <?php foreach ($choices as $choice): ?>
                                <th class="border p-2"><?= htmlspecialchars($choice->title) ?></th>
                            <?php endforeach; ?>
                            <th class="border p-2">Victoires</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($choices as $choice): ?>
                            <tr>
                                <td class="border p-2 font-bold"><?= htmlspecialchars($choice->title) ?></td>
                                <?php foreach ($choices as $opponent): ?>
                                    <td class="border p-2 text-center">
                                        <?php if ($choice->id === $opponent->id): ?>
                                            -
                                        <?php else: ?>
                                            <?= $matrix[$choice->id][$opponent->id] ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="border p-2 text-center"><?= $choiceScores[$choice->id] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html> 