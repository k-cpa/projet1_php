<?php 
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');
        
    if(isset($_GET['id'])) {
        $film_id = htmlspecialchars($_GET['id']);

        $request = $bdd->prepare (' SELECT * 
                                    FROM fiche_film
                                    WHERE film_id = :film_id
                                ');
        $request -> execute(['film_id' => $film_id]);
        $film_data = $request -> fetch();

        



        if(isset($_POST['title']) && isset($_POST['duration']) && isset($_POST['date'])) {
            $title = htmlspecialchars($_POST['title']);
            $duration = htmlspecialchars($_POST['duration']);
            $date = htmlspecialchars($_POST['date']);

            $updateRequest = $bdd->prepare(' UPDATE fiche_film
                                        SET title = :title, duration = :duration, date = :date
                                        WHERE film_id = :film_id
                                    ');
            $success = $updateRequest->execute([
                'title' => $title,
                'duration' => $duration,
                'date' => $date,
                'film_id' => $film_id,
            ]);
            if ($success) {
                header ('location: modify.php?id=$film_id');
                echo "La modification a été effectuée";
            } else {
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