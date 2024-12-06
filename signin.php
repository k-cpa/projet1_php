<?php 
    session_start();
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');

    // check si données dans le form sont envoyées
    if (isset($_POST['mail']) && isset($_POST['username']) && isset($_POST['password'])) {
        $mail = htmlspecialchars($_POST['mail']);
        $username = htmlspecialchars($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Vérification existance username et email dans la BDD
        $checkUser = $bdd->prepare  ('  SELECT * 
                                        FROM users
                                        WHERE mail = :mail OR username = :username
                                    ');
        $checkUser->execute([
            'mail' => $mail,
            'username' => $username
        ]);
        if ($checkUser -> rowCount() > 0) {
            echo "Ce nom d'utilisateur ou cet adresse mail existe déjà";
        } else {
            // Insertion données dans la BDD
            $insertUser = $bdd->prepare ('  INSERT INTO users (mail, username, `password`)
                                            VALUES (:mail, :username, :password)
                                        ');
            $insertUser->execute([
                'mail' => $mail,
                'username' => $username,
                'password' => $password
            ]);
            header("location:login.php");
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
    <h1>Inscription</h1>
    <form action="signin.php" method="post">
        <label for="mail">Veuillez entre un mail valide :</label>
        <input type="email" id="mail" name="mail" required>
        <label for="username">Veuillez saisir un nom d'utilisateur : </label>
        <input type="text" id="username" name="username" required>
        <label for="password">Saisir votre mot de passe :</label>
        <input type="text" id="password" name="password" required>
        <button>S'inscrire</button>
    </form>
</body>
</html>