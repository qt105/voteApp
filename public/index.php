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
    
    require_once __DIR__ . "/../templates/header.html.php";

    require_once __DIR__ . "/../vendor/autoload.php";

    //use App\VoteApp\ClassA;
    //use App\VoteApp\ClassB;
    use App\VoteApp\ClassUser;

    //$a = new ClassA();
    //$b = new ClassA();
    //$c = new ClassB();

    //$result = $a->f(36);
    //echo "<p>".$result."</p>";
    //$result2 = $c->f(36);
    //echo "<p>".$result2."</p>";

    if (isset($_SESSION['user'])) {
    
        $user = unserialize($_SESSION['user']);
        //var_dump($user); die;

        require_once "../templates/profile.html.php"; 
    } else {
        echo "<h2>Pas d'utilisateur</h2>";
    }
    
    ?>
    <a href="new_user.php">User Form</a>
    <a href="createUser.php">Create user</a>
</body>
</html>

