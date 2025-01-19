<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    session_start();

    include __DIR__ . "/../config/database.php";
    
    require_once __DIR__ . "/../templates/header.html.php";

    require_once __DIR__ . "/../vendor/autoload.php";

    use App\VoteApp\ClassUser;

    if (isset($_SESSION['user'])) {
    
        $user = unserialize($_SESSION['user']);
        //var_dump($user); die;

        require_once "../templates/profile.html.php"; 
    } else {
        echo "<h2>Pas d'utilisateur</h2>";
    }
    
$query = "SELECT * FROM forms WHERE start_date <= NOW() AND end_date >= NOW() ORDER BY start_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$openForms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
    <main>
        <?php if ($openForms): ?>
            <ul>
                <?php foreach ($openForms as $form): ?>
                    <li>
                        <h2><?= htmlspecialchars($form['question']) ?></h2>
                        <p><?= htmlspecialchars($form['description']) ?></p>
                        <p><strong>Début:</strong> <?= $form['start_date'] ?> <strong>Fin:</strong> <?= $form['end_date'] ?></p>
                        <p><strong>Durée du vote:</strong> <?= $form['voting_duration'] ?> minutes</p>
                        <a href="vote.php?id=<?= $form['id'] ?>">Participer au vote</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun vote ouvert pour le moment.</p>
        <?php endif; ?>
    </main>
</body>
</html>
</body>
</html>

