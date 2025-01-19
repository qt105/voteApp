<?php
// Include your database configuration
include __DIR__ . "/../config/database.php";

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from form
    $question = $_POST['question'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $voting_duration = $_POST['voting_duration'];
    $choice_count = $_POST['choice_count'];

    // Insert data into the forms table
    $query = "INSERT INTO forms (question, description, start_date, end_date, voting_duration, choice_count) 
              VALUES (:question, :description, :start_date, :end_date, :voting_duration, :choice_count)";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':question', $question);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':voting_duration', $voting_duration);
    $stmt->bindParam(':choice_count', $choice_count);

    if ($stmt->execute()) {
        $success_message = "Le vote a été créé avec succès!";
    } else {
        $error_message = "Une erreur est survenue lors de la création du vote.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Vote</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <header>
        <h1>Créer un Nouveau Vote</h1>
    </header>

    <main>
        <!-- Display success or error message -->
        <?php if (isset($success_message)): ?>
            <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
        <?php elseif (isset($error_message)): ?>
            <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <!-- Form to create a vote -->
        <form method="POST" action="createVote.php">
            <div>
                <label for="question">Question</label>
                <input type="text" name="question" id="question" required />
            </div>

            <div>
                <label for="description">Description</label>
                <input type="text" name="description" id="description" />
            </div>

            <div>
                <label for="start_date">Date de début</label>
                <input type="datetime-local" name="start_date" id="start_date" required />
            </div>

            <div>
                <label for="end_date">Date de fin</label>
                <input type="datetime-local" name="end_date" id="end_date" required />
            </div>

            <div>
                <label for="voting_duration">Durée de vote (en minutes)</label>
                <input type="number" name="voting_duration" id="voting_duration" min="1" required />
            </div>

            <div>
                <label for="choice_count">Nombre de choix possibles</label>
                <select id="choice_count" name="choice_count" required>
                    <option value="">Sélectionnez</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>

            <button type="submit">Créer le vote</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 Condorcet Voting App</p>
    </footer>
</body>
</html>
