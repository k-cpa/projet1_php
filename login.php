<?php 
    // Démarrage de la session pour utiliser les variables de session
    session_start();

    // Connexion à la base de données MySQL avec PDO
    // 'localhost' est l'hôte, 'film' est le nom de la base de données, 'root' est l'utilisateur, et 'root' est le mot de passe.
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');

    // Vérifie si les données du formulaire (username et password) sont envoyées via POST
    if(isset($_POST['username']) && isset($_POST['password'])) {
        // Récupération des données envoyées par le formulaire
        // htmlspecialchars() est utilisé pour éviter les failles XSS (protection contre l'injection de code HTML/JS)
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        // Préparation d'une requête SQL pour rechercher un utilisateur correspondant au nom d'utilisateur saisi
        // L'utilisation de `:username` protège contre les injections SQL (paramètres préparés)
        $connectUser = $bdd->prepare(' 
                                        SELECT * 
                                        FROM users 
                                        WHERE username = :username 
                                    ');
        // Exécution de la requête avec le paramètre fourni
        $connectUser->execute([
            'username' => $username
        ]);

        // Récupération de la première ligne de résultat (un seul utilisateur devrait correspondre)
        $user = $connectUser->fetch();

        // Vérifie si un utilisateur a été trouvé
        if($user) {
            // Vérifie si le mot de passe saisi correspond au mot de passe stocké
            // `password_verify()` compare le mot de passe saisi en clair avec le hash enregistré en base
            if (password_verify($password, $user['password'])) {
                // Si le mot de passe est correct
                echo "Connexion réussie";

                // Enregistre les informations utilisateur dans la session pour maintenir la connexion
                $_SESSION['id'] = $user['user_id']; // L'ID unique de l'utilisateur
                $_SESSION['username'] = $user['username']; // Le nom d'utilisateur

                // Redirige l'utilisateur vers la page d'accueil `index.php` après connexion
                header('location:index.php');
                exit; // Terminer l'exécution pour éviter de continuer après la redirection
            } else {
                // Message affiché si le mot de passe est incorrect
                echo "Mot de passe incorrect";
            }
        } else {
            // Message affiché si aucun utilisateur correspondant n'est trouvé
            echo "Identifiant incorrect";
        }
    }
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
<?php include('nav.php');?>
    <h1>Connexion</h1>
    <form action="login.php" method="post">
        <label for="username">Identifiant :</label>
        <input type="text" id="username" name="username">
        <label for="password">Mot de passe :</label>
        <input type="text" id="password" name="password">
        <button type="submit">Connexion</button>
    </form>
    <a href="">Mot de passe oublié</a>
</body>
</html>