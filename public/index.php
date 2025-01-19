<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php
    session_start();

    include __DIR__ . "/../config/database.php";

    require_once __DIR__ . "/../templates/header.html.php";

    require_once __DIR__ . "/../vendor/autoload.php";

    use App\VoteApp\ClassUser;

    if (isset($_SESSION["user"])) {
        $user = unserialize($_SESSION["user"]);
        //var_dump($user); die;

        require_once "../templates/profile.html.php";
    } else {
        echo "<div class='min-h-screen flex items-center justify-center'><h2 class='text-2xl font-bold text-gray-800 my-4'>Pas d'utilisateur</h2></div>";
    }
    ?>
    <div class="flex justify-center">
        <div class="flex gap-4">
            <a href="new_user.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">User Form</a>
            <a href="createUser.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create user</a>
        </div>
    </div>
</body>
</html>
