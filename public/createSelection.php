<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginUser.php");
    exit;
}

include __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../templates/header.html.php";
require_once __DIR__ . "/../vendor/autoload.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (empty($_POST['title']) || empty($_POST['start_date']) || 
        empty($_POST['end_date']) || empty($_POST['choices'])) {
        $error = "Tous les champs sont obligatoires";
    } else {
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Insert consultation
            $stmt = $pdo->prepare("INSERT INTO consultations (title, description, creator_id, start_date, end_date, status) 
                                 VALUES (:title, :description, :creator_id, :start_date, :end_date, 'active')");
            
            $stmt->execute([
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'creator_id' => $_SESSION['user_id'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date']
            ]);
            
            $consultationId = $pdo->lastInsertId();
            
            // Insert choices
            $stmtChoice = $pdo->prepare("INSERT INTO choices (consultation_id, title) VALUES (:consultation_id, :title)");
            
            foreach ($_POST['choices'] as $choice) {
                $stmtChoice->execute([
                    'consultation_id' => $consultationId,
                    'title' => $choice
                ]);
            }
            
            $pdo->commit();
            header("Location: consultation.php?id=" . $consultationId);
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Une erreur est survenue lors de la création de la consultation";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une consultation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6">Créer une nouvelle consultation</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Titre</label>
                <input type="text" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Date de début</label>
                <input type="datetime-local" name="start_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Date de fin</label>
                <input type="datetime-local" name="end_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div id="choices-container">
                <label class="block text-sm font-medium text-gray-700">Choix</label>
                <div class="space-y-2">
                    <div class="flex gap-2">
                        <input type="text" name="choices[]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <button type="button" onclick="addChoice()" class="bg-blue-500 text-white px-4 py-2 rounded">+</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Créer la consultation
            </button>
        </form>
    </div>

    <script>
        function addChoice() {
            const container = document.querySelector('#choices-container .space-y-2');
            const newChoice = document.createElement('div');
            newChoice.className = 'flex gap-2';
            newChoice.innerHTML = `
                <input type="text" name="choices[]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-4 py-2 rounded">-</button>
            `;
            container.appendChild(newChoice);
        }
    </script>
</body>
</html>