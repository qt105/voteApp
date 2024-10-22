<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php

    require_once __DIR__ . "/../vendor/autoload.php";

    use App\VoteApp\ClassA;
    use App\VoteApp\ClassB;
    use App\VoteApp\ClassUser;

    $a = new ClassA();
    $b = new ClassA();
    $c = new ClassB();

    $result = $a->f(36);
    echo "<p>".$result."</p>";
    $result2 = $c->f(36);
    echo "<p>".$result2."</p>";

    $user = new ClassUser("John", "Doe", "johndoe", "john.doe@example.com", "securePassword123!", "admin");

    require_once "../templates/profile.html.php";
    ?>
    <a href="new_user.php">User Form</a>
</body>
</html>

