<?php 
    session_start();
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');

    if(isset($_POST['username']) && isset($_POST['password'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        $connectUser = $bdd->prepare (' SELECT * 
                                        FROM users 
                                        WHERE username = :username 
                                    ');
        $connectUser->execute([
            'username' => $username
        ]);

        $user = $connectUser->fetch();

        // on check si user existe
        if($user) {
            if (password_verify($password, $user['password'])) {
                echo "Connexion réussie";
                $_SESSION['id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header('location:index.php');
                exit;
            } else {
                echo "Mot de passe incorrect";
            }
        } else {
            // Si user non reconnu
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