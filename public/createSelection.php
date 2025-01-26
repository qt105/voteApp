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

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Insert consultation
        $stmt = $pdo->prepare("
            INSERT INTO consultations (
                title, description, creator_id, start_date, end_date, status
            ) VALUES (?, ?, ?, ?, ?, 'active')
        ");
        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_SESSION['user_id'],
            $_POST['start_date'],
            $_POST['end_date']
        ]);
        
        $consultationId = $pdo->lastInsertId();

        // Insert choices
        $stmt = $pdo->prepare("
            INSERT INTO choices (consultation_id, title, description)
            VALUES (?, ?, ?)
        ");

        foreach ($_POST['choices'] as $choice) {
            if (!empty($choice['title'])) {
                $stmt->execute([
                    $consultationId,
                    $choice['title'],
                    $choice['description'] ?? null
                ]);
            }
        }

        $pdo->commit();
        header("Location: consultation.php?id=" . $consultationId);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Une erreur est survenue lors de la création de la consultation.";
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
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Créer une nouvelle consultation</h1>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="consultationForm">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                        Titre
                    </label>
                    <input type="text" id="title" name="title" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="start_date">
                            Date de début
                        </label>
                        <input type="datetime-local" id="start_date" name="start_date" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="end_date">
                            Date de fin
                        </label>
                        <input type="datetime-local" id="end_date" name="end_date" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <div class="mb-4">
                    <h2 class="text-xl font-bold mb-4">Options</h2>
                    <div id="choicesContainer">
                        <!-- Initial choice fields will be added here -->
                    </div>
                    <button type="button" onclick="addChoice()"
                            class="mt-2 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Ajouter une option
                    </button>
                </div>

                <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Créer la consultation
                </button>
            </form>
        </div>
    </div>

    <script>
        let choiceCount = 0;

        function addChoice() {
            const container = document.getElementById('choicesContainer');
            const choiceDiv = document.createElement('div');
            choiceDiv.className = 'mb-4 p-4 border rounded';
            choiceDiv.innerHTML = `
                <div class="mb-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Titre de l'option
                    </label>
                    <input type="text" name="choices[${choiceCount}][title]" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Description de l'option
                    </label>
                    <textarea name="choices[${choiceCount}][description]"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <button type="button" onclick="this.parentElement.remove()"
                        class="mt-2 bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                    Supprimer
                </button>
            `;
            container.appendChild(choiceDiv);
            choiceCount++;
        }

        // Add initial choices
        addChoice();
        addChoice();
    </script>
</body>
</html>