<?php 
    // Démarre une session, cela permet d'utiliser des variables de session (utile pour gérer l'état de l'utilisateur)
    session_start();

    // Connexion à la base de données avec PDO 
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');

    // Sécurisation de l'ID du film récupéré depuis l'URL avec la méthode GET
    // htmlspecialchars() transforme les caractères spéciaux en entités HTML pour éviter les attaques XSS
    $film_id = htmlspecialchars($_GET['id']);
    
    // Vérifie si un ID est passé dans l'URL (via méthode GET)
    if (isset($_GET['id'])) { 
        // Prépare une requête SQL pour sélectionner toutes les informations du film dont l'ID correspond
        $request = $bdd->prepare('
            SELECT *
            FROM fiche_film
            WHERE film_id = :film_id
        ');

        // Exécute la requête en passant l'ID du film comme paramètre
        $request->execute(['film_id' => $film_id]);

        // Récupère les données du film sous forme de tableau associatif
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