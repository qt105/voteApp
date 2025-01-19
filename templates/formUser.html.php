<html>
    <body>
        <form action="createUser.php" method="post">
            <label for="userame">Nom d'utilisateur</label>
                <input type="text" name="username" id="username" required/>
            </div>
            <div>
                <label for="firstName">Prénom</label>
                <input type="text" name="firstName" id="firstName" />
            </div>
            <div>
                <label for="lastName">Nom de famille</label>
                <input type="text" name="lastName" id="lastName" />
            </div>
            <div>
            <div>
                <label for="email">Adresse Mail</label>
                <input type="email" name="email" id="email" />
            </div>
            <div>
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required/>
            </div>
            <div>
                <input type="submit" name="submit" id="submit" value="Créer" />
            </div>
        </form>
    </body>
</html>