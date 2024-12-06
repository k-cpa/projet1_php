<?php 
    session_start();
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');

    if (isset($_GET['id'])) {
        $film_id = htmlspecialchars($_GET['id']);

        // Récupère nom du fichier image avant suppress
        $imgRequest = $bdd->prepare ('  SELECT image 
                                        FROM fiche_film
                                        WHERE film_id = :film_id
                                    ');
        $imgRequest->execute(['film_id' => $film_id]);
        $data = $imgRequest->fetch();

        // Suppression de l'image dans le folder images
        if($data['image']) {
            $imagePath = $data['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $request = $bdd->prepare ('DELETE 
                                    FROM fiche_film
                                    WHERE film_id = :film_id
                                ');
        $request->execute(['film_id' => $film_id]);
        header ("Location:index.php");
        exit;
    }
?>