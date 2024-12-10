<?php 
    session_start();
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');
    var_dump($_SESSION);

    $request = $bdd->prepare(' SELECT *
                                FROM fiche_film'
                            );
    $request->execute([]);
    // var_dump($_SESSION); // test fonctionnement session
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Projet 1</title>
</head>
<body>
    <h1>Récupération de la requête</h1>
    <?php include('nav.php');?>
                        <?php 
                            while($data = $request->fetch()): 
                            $min = $data['duration'] % 60;
                            $heure = ($data['duration'] - $min) / 60
                        ?>
                                <article>
                                    <img src="images/<?= $data['image'] ?>" alt="Image correspondant au film">
                                    <p><?= $data['title'] ?></p>
                                    <p><?= $heure . "h" . $min . "min" ?></p>
                                    <p><?= $data['date'] ?></p>
                                    <a href="voir_plus.php?id=<?= htmlspecialchars($data['film_id']) ?>">voir plus</a>
                                    <a href="modify.php?id=<?= htmlspecialchars($data['film_id']) ?>">Modifier</a>
                                    <a href="suppress.php?id=<?= htmlspecialchars($data['film_id']) ?>">Supprimer</a>
                                </article>
                        <?php endwhile?>
                        
</body>
</html>