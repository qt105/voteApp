<?php
session_start();
require_once __DIR__ . "/../templates/header.html.php";
require_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$consultationId = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT c.*, u.username as creator_name,
           (SELECT COUNT(*) FROM votes v WHERE v.consultation_id = c.id) as vote_count,
           (SELECT COUNT(*) FROM votes v WHERE v.consultation_id = c.id AND v.voter_id = ?) as has_voted
    FROM consultations c
    JOIN users u ON c.creator_id = u.id
    WHERE c.id = ?
");
$stmt->execute([isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null, $consultationId]);
$consultation = $stmt->fetch();

if (!$consultation) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM choices WHERE consultation_id = ?");
$stmt->execute([$consultationId]);
$choices = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($consultation->title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($consultation->title) ?></h1>
            
            <div class="mb-6">
                <p class="text-gray-600">
                    Créé par: <?= htmlspecialchars($consultation->creator_name) ?><br>
                    Date de début: <?= $consultation->start_date ?><br>
                    Date de fin: <?= $consultation->end_date ?><br>
                    Nombre de votes: <?= $consultation->vote_count ?>
                </p>
            </div>

            <?php if ($consultation->description): ?>
                <div class="mb-6">
                    <h2 class="text-xl font-bold mb-2">Description</h2>
                    <p class="text-gray-600"><?= nl2br(htmlspecialchars($consultation->description)) ?></p>
                </div>
            <?php endif; ?>

            <div class="mb-6">
                <h2 class="text-xl font-bold mb-2">Options</h2>
                <ul class="list-disc pl-5">
                    <?php foreach ($choices as $choice): ?>
                        <li class="mb-2">
                            <span class="font-semibold"><?= htmlspecialchars($choice->title) ?></span>
                            <?php if ($choice->description): ?>
                                <p class="text-gray-600 ml-2"><?= htmlspecialchars($choice->description) ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="flex gap-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($consultation->status === 'active' && !$consultation->has_voted): ?>
                        <a href="vote.php?id=<?= $consultation->id ?>" 
                           class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Voter
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="loginUser.php" 
                       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Se connecter pour voter
                    </a>
                <?php endif; ?>
                
                <?php if ($consultation->vote_count > 0): ?>
                    <a href="results.php?id=<?= $consultation->id ?>" 
                       class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Voir les résultats
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 