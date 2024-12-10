<?php 
    // Connexion à la base de données MySQL avec PDO
    // 'film' est le nom de la base de données, 'root' est l'utilisateur et 'root' est le mot de passe
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');
        
    // Vérifie si l'ID du film est passé en paramètre GET dans l'URL
    if(isset($_GET['id'])) {
        // Sécurise l'ID en échappant les caractères spéciaux pour éviter les failles XSS
        $film_id = htmlspecialchars($_GET['id']);
        $user_id = $_SESSION['id'];

        // Prépare une requête SQL pour récupérer les informations du film correspondant à cet ID
        // Utilisation d'un placeholder `:film_id` pour protéger contre les injections SQL
        $request = $bdd->prepare(' 
                                    SELECT * 
                                    FROM fiche_film
                                    WHERE film_id = :film_id AND user_id = :user_id
                                ');
        // Exécution de la requête avec l'ID fourni
        $request->execute([
            'film_id' => $film_id,
            'user_id' => $user_id
        ]);

        // Récupère les données du film sous forme de tableau associatif
        $film_data = $request->fetch();

        // Si les données du formulaire sont envoyées via POST
        if(isset($_POST['title']) && isset($_POST['duration']) && isset($_POST['date'])) {
            // Récupère les valeurs du formulaire en les sécurisant avec htmlspecialchars()
            $title = htmlspecialchars($_POST['title']); // Le nouveau titre du film
            $duration = htmlspecialchars($_POST['duration']); // La nouvelle durée du film
            $date = htmlspecialchars($_POST['date']); // La nouvelle date de sortie

            // Prépare une requête SQL pour mettre à jour les informations du film
            $updateRequest = $bdd->prepare(' 
                                        UPDATE fiche_film
                                        SET title = :title, duration = :duration, date = :date
                                        WHERE film_id = :film_id AND user_id = :user_id
                                    ');
            // Exécute la requête avec les nouvelles données
            $success = $updateRequest->execute([
                'title' => $title,
                'duration' => $duration,
                'date' => $date,
                'film_id' => $film_id, // L'ID du film à mettre à jour
                'user_id' => $user_id // User ID lié à la création
            ]);

            // Vérifie si la mise à jour a réussi
            if ($success) {
                // Redirige vers la même page pour afficher les modifications
                header('location: modify.php?id=' . $film_id);
                echo "La modification a été effectuée";
            } else {
                // Affiche un message d'erreur si la mise à jour échoue
                echo "Une erreur est survenue pendant la mise à jour";
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier</title>
</head>
<body>
    <h1>Modifier les informations du film</h1>
    <form action="modify.php?id=<?= $film_id ?>" method="post">
        <label for="title">Titre du film :</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($film_data['title'] ?? '') ?>" required>
        <label for="duration">La durée du film en minutes :</label>
        <input type="number" id="duration" name="duration" value ="<?= htmlspecialchars($film_data['duration'] ?? '') ?>" required>
        <label for="date">Date de sortie du film :</label>
        <input type="number" id="date" name="date" value="<?= htmlspecialchars($film_data['date'] ?? '') ?>" required> 
        <button type="submit">Modifier</button>
    </form>
    <a href="index.php">Retour à la liste des films</a>
</body>
</html>