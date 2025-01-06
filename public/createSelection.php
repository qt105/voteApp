<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Vote</title>
    <script>
        function updateChoices() {
            const choiceCount = document.getElementById('choice_count').value;
            const choicesContainer = document.getElementById('choices_container');
            choicesContainer.innerHTML = ''; // Réinitialiser le conteneur

            for (let i = 0; i < choiceCount; i++) {
                const choiceDiv = document.createElement('div');
                choiceDiv.innerHTML = `<label for="choice_${i}">Choix ${i + 1}</label>
                                       <input type="text" name="choices[]" id="choice_${i}" required />`;
                choicesContainer.appendChild(choiceDiv);
            }
        }
    </script>
</head>
<body>
    <form method="POST" action="votre_script.php">
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
            <select id="choice_count" name="choice_count" onchange="updateChoices()" required>
                <option value="">Sélectionnez</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>

        <div id="choices_container">
            <!-- Les choix seront ajoutés ici dynamiquement -->
        </div>

        <button type="submit">Créer le vote</button>
    </form>

    <?php
    include __DIR__ . "/../config/database.php";
    require_once __DIR__ . "/../templates/header.html.php";
    require_once __DIR__ . "/../vendor/autoload.php";
    use App\VoteApp\ClassUser;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Traitez les données ici
        var_dump($_REQUEST);
        // Vous pouvez également valider les dates et gérer la logique de vote ici
    }
    ?>
</body>
</html>