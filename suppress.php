<?php 
    // Démarre une session pour utiliser les variables de session (utile pour la gestion de l'utilisateur)
    session_start();

    // Connexion à la base de données MySQL avec PDO
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');

    // Vérifie si l'ID du film est passé dans l'URL via la méthode GET
    if (isset($_GET['id'])) {
        // Récupère et sécurise l'ID du film passé dans l'URL pour éviter les attaques XSS
        $film_id = htmlspecialchars($_GET['id']);

        // Récupère le nom du fichier image associé au film avant de le supprimer
        $imgRequest = $bdd->prepare('
            SELECT image 
            FROM fiche_film
            WHERE film_id = :film_id
        ');
        // Exécute la requête pour obtenir l'image associée au film
        $imgRequest->execute([
            'film_id' => $film_id,
        ]);
        $data = $imgRequest->fetch();

        // Si une image est associée au film (c'est-à-dire que l'image existe dans la base de données)
        if($data['image']) {
            // On construit le chemin du fichier image à partir du nom récupéré dans la base de données
            $imagePath = 'images/' . $data['image'];

            // Si le fichier image existe dans le dossier 'images'
            if (file_exists($imagePath)) {
                // Utilise la fonction PHP unlink() pour supprimer physiquement le fichier du serveur
                unlink($imagePath);
            }
        }

        // Prépare la requête SQL pour supprimer le film de la base de données
        $request = $bdd->prepare('
            DELETE 
            FROM fiche_film
            WHERE film_id = :film_id 
        ');
        // Exécute la requête pour supprimer le film de la base de données
        $request->execute([
            'film_id' => $film_id,
        ]);

        // Redirige l'utilisateur vers la page index.php après la suppression du film
        header("Location:index.php");
        exit; // Arrête l'exécution du script pour éviter tout traitement supplémentaire
    }
?>
