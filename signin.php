<?php 
    // Démarre la session pour permettre l'accès aux variables de session
    session_start();
    
    // Connexion à la base de données avec PDO 
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');

    // Vérifie si les données du formulaire sont envoyées via POST
    if (isset($_POST['mail']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirmPassword'])) {
        // Récupère les données envoyées par l'utilisateur et les sécurise avec htmlspecialchars pour éviter les injections XSS
        $mail = htmlspecialchars($_POST['mail']);
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $confirmPassword = htmlspecialchars($_POST['confirmPassword']);

        // Vérifie si le mot de passe et la confirmation du mot de passe sont identiques
        if($password != $confirmPassword) {
            // Si les mots de passe ne correspondent pas, affiche un message d'erreur
            echo "Veuillez saisir un mot de passe identique";
        } else {
            // Si les mots de passe correspondent, on vérifie que le mail ou le nom d'utilisateur n'existe pas déjà dans la base de données
            $checkUser = $bdd->prepare('
                SELECT *
                FROM users
                WHERE mail = :mail OR username = :username
            ');
            // Exécute la requête pour vérifier l'existence du mail ou du nom d'utilisateur
            $checkUser->execute([
                'mail' => $mail,
                'username' => $username
            ]);

            // Si un utilisateur existe déjà avec ce mail ou ce nom d'utilisateur, on affiche un message d'erreur
            if ($checkUser->rowCount() > 0) {
                echo "Ce nom d'utilisateur ou cette adresse mail existe déjà";
            } else {
                // Si aucun utilisateur avec ce mail ou nom d'utilisateur n'est trouvé, on crypte le mot de passe avant de l'enregistrer
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

                // Prépare la requête pour insérer les données de l'utilisateur dans la base de données
                $insertUser = $bdd->prepare('
                    INSERT INTO users (mail, username, `password`)
                    VALUES (:mail, :username, :password)
                ');
                // Exécute la requête d'insertion dans la base de données
                $insertUser->execute([
                    'mail' => $mail,
                    'username' => $username,
                    'password' => $hashedPassword
                ]);

                // Redirige l'utilisateur vers la page de connexion après l'enregistrement réussi
                header('location: login.php');
                exit; // Arrête l'exécution du script après la redirection
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Inscription</title>
</head>
<body>
<?php include('nav.php');?>
    <h1>Inscription</h1>
    <form action="signin.php" method="post">
        <label for="mail">Veuillez entre un mail valide :</label>
        <input type="email" id="mail" name="mail" required>
        <label for="username">Veuillez saisir un nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Saisir votre mot de passe :</label>
        <input type="text" id="password" name="password" required>
        <label for="confirmPassword">Confirmez votre mot de passe :</label>
        <input type="text" id="confirmPassword" name="confirmPassword" required>
        <button>S'inscrire</button>
    </form>
</body>
</html>