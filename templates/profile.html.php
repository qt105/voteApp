<html>
    <head></head>
    <body>
        <header>
        </header>
        <main>
            <h1>Profil de <?= $user->getFirstName() . " " . $user->getLastName(); ?></h1>
            <div>
                <p>Username: <?= $user->getUsername(); ?></p>
                <p>Email: <?= $user->getEmail(); ?></p>
                <p>Role: <?= $user->getRole(); ?></p>
            </div>
        </main>
    </body>
</html>