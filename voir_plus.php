<?php 
    session_start()
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');
    $film_id = htmlspecialchars($_GET['id']);
    
    if(isset($_GET['id'])){ 
        $request = $bdd->prepare(' SELECT *
                                    FROM fiche_film
                                    WHERE film_id = :film_id
                                ');
        $request->execute(['film_id' => $film_id]);
        $data = $request->fetch();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <?php include('nav.php');?>
    <!-- Récupérer l'id de la fiche de film selectionné  
     l'utiliser dans une requete SQL select pour afficher une seule fiche de film -->
     <article>
        <p><?= $data['title'] ?></p>
        <p><?= $data['duration'] ?></p>
        <p><?= $data['date'] ?></p>
     </article>
    
</body>
</html>