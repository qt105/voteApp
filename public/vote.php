<?php
session_start();
require_once __DIR__ . "/../templates/header.html.php";
require_once __DIR__ . "/../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: loginUser.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$consultationId = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT c.*, 
           (SELECT COUNT(*) FROM votes v WHERE v.consultation_id = c.id AND v.voter_id = ?) as has_voted
    FROM consultations c 
    WHERE c.id = ? AND c.status = 'active'
    AND NOW() BETWEEN c.start_date AND c.end_date
");
$stmt->execute([$_SESSION['user_id'], $consultationId]);
$consultation = $stmt->fetch();

if (!$consultation || $consultation->has_voted > 0) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM choices WHERE consultation_id = ?");
$stmt->execute([$consultationId]);
$choices = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO votes (consultation_id, voter_id) VALUES (?, ?)");
        $stmt->execute([$consultationId, $_SESSION['user_id']]);
        $voteId = $pdo->lastInsertId();

        $rankings = $_POST['rankings'];
        for ($i = 0; $i < count($rankings); $i++) {
            for ($j = $i + 1; $j < count($rankings); $j++) {
                $stmt = $pdo->prepare("
                    INSERT INTO vote_details (vote_id, winner_choice_id, loser_choice_id)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$voteId, $rankings[$i], $rankings[$j]]);
            }
        }
        
        $pdo->commit();
        header("Location: results.php?id=" . $consultationId);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Une erreur est survenue lors de l'enregistrement du vote.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter - <?= htmlspecialchars($consultation->title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($consultation->title) ?></h1>
            
            <form method="POST" id="voteForm">
                <div class="mb-4">
                    <p class="text-gray-600 mb-2">
                        Classez les options par ordre de préférence (glissez-déposez pour réorganiser)
                    </p>
                    <ul id="choicesList" class="space-y-2">
                        <?php foreach ($choices as $choice): ?>
                            <li class="p-4 bg-gray-50 rounded border cursor-move">
                                <?= htmlspecialchars($choice->title) ?>
                                <input type="hidden" name="rankings[]" value="<?= $choice->id ?>">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Voter
                </button>
            </form>
        </div>
    </div>

    <script>
        new Sortable(document.getElementById('choicesList'), {
            animation: 150,
            ghostClass: 'bg-gray-200'
        });
    </script>
</body>
</html> 