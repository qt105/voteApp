<?php
session_start();
require_once __DIR__ . "/../templates/header.html.php";
require_once __DIR__ . "/../config/database.php";

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginUser.php");
    exit;
}

// Récupérer les statistiques
$mostParticipatedConsultations = $pdo->query("
    SELECT c.title, COUNT(v.id) as participant_count
    FROM consultations c
    LEFT JOIN votes v ON c.id = v.consultation_id
    GROUP BY c.id
    ORDER BY participant_count DESC
    LIMIT 5
")->fetchAll();

$voteDistributionByBirthYear = $pdo->query("
    SELECT YEAR(u.birth_date) as birth_year, COUNT(v.id) as vote_count
    FROM votes v
    JOIN users u ON v.voter_id = u.id
    GROUP BY birth_year
    ORDER BY birth_year
")->fetchAll();

$averageOptionsPerConsultation = $pdo->query("
    SELECT AVG(option_count) as average_options
    FROM (
        SELECT COUNT(*) as option_count
        FROM choices
        GROUP BY consultation_id
    ) as option_counts
")->fetch();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 mt-16">
        <h1 class="text-3xl font-bold mb-4">Statistiques d'utilisation</h1>

        <h2 class="text-2xl font-bold mt-6">Top 5 des scrutins avec le plus de participants</h2>
        <ul class="list-disc pl-5">
            <?php foreach ($mostParticipatedConsultations as $consultation): ?>
                <li><?= htmlspecialchars($consultation->title) ?> - Participants: <?= $consultation->participant_count ?></li>
            <?php endforeach; ?>
        </ul>

        <h2 class="text-2xl font-bold mt-6">Répartition des votes par année de naissance</h2>
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="border p-2">Année de naissance</th>
                    <th class="border p-2">Nombre de votes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($voteDistributionByBirthYear as $row): ?>
                    <tr>
                        <td class="border p-2"><?= $row->birth_year ?></td>
                        <td class="border p-2"><?= $row->vote_count ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 class="text-2xl font-bold mt-6">Nombre moyen d'options par scrutin</h2>
        <p>Nombre moyen d'options : <?= round($averageOptionsPerConsultation->average_options, 2) ?></p>
    </div>
</body>
</html> 