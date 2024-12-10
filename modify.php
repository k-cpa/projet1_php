<?php 
    // Démarre une session pour utiliser les variables de session (utile pour la gestion de l'utilisateur)
    session_start();
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

        if (isset($_FILES['image'])) { // Vérifie si un fichier a été soumis via un champ de type file
            $fileName = $_FILES['image']['name']; // Nom original du fichier envoyé
            $tmpName = $_FILES['image']['tmp_name']; // Chemin temporaire où le fichier est stocké
            $location = 'images/'; // Dossier de destination pour stocker les fichiers téléchargés
            $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // Extension du fichier en minuscule
        
            // Tableau pour spécifier les extensions autorisées
            $autorizedExtension = ['png', 'jpeg', 'jpg', 'webp', 'bmp', 'svg'];
        
            // Vérifie si l'extension du fichier est dans la liste des extensions autorisées
            if (in_array($imageFileType, $autorizedExtension)) {
                // Génère un nom de fichier unique pour éviter les conflits
                $uniqueFileName = uniqid('Image_', true) . '.' . $imageFileType;
        
                // Construit le chemin complet où le fichier sera déplacé
                $image = $location . $uniqueFileName;
            } else {
                // Affiche un message si l'extension n'est pas autorisée
                echo 'Veuillez choisir une image au format JPG, JPEG, PNG ou GIF';
            }
        
            // Si un film existe déjà avec une image on supprime l'ancienne image
            if ($film_data && !empty($film_data['image'])) {
                // récupère chemin complet de l'ancienne image en ajoutant le chemin du dossier
                $oldImage = $location.$film_data['image'];

                // Vérifie si le fichier de l'ancienne image existe sur le serveur
                if (file_exists($oldImage)) {
                    // Si le fichier existe = suppression de l'image avec fonction 'unlink()' / Fonction qui sert à supprimer physiquement le fichier du serveur
                    unlink($oldImage); // Supprime l'ancienne image
                }
            }
            // Déplace le fichier depuis le chemin temporaire vers le dossier cible
            if (move_uploaded_file($tmpName, $image)) {
                // Si le téléchargement réussit, affiche une balise <img> pour prévisualiser l'image
                echo "<img src='" . $image . "' alt='Image du film'>";
            } else {
                // Affiche un message si le déplacement échoue
                echo "Erreur lors du téléchargement";
            }
        }

        // Si les données du formulaire sont envoyées via POST
        if(isset($_POST['title']) && isset($_POST['duration']) && isset($_POST['date'])) {
            // Récupère les valeurs du formulaire en les sécurisant avec htmlspecialchars()
            $title = htmlspecialchars($_POST['title']); // Le nouveau titre du film
            $duration = htmlspecialchars($_POST['duration']); // La nouvelle durée du film
            $date = htmlspecialchars($_POST['date']); // La nouvelle date de sortie

            // Si uniqueFile est défini et non nul on l'utilise sinon on utilise film_data['image']
            $imageUpdate = isset($uniqueFileName) ? $uniqueFileName : $film_data['image'];

            // Prépare une requête SQL pour mettre à jour les informations du film
            $updateRequest = $bdd->prepare(' 
                                        UPDATE fiche_film
                                        SET title = :title, duration = :duration, date = :date, image = :image
                                        WHERE film_id = :film_id AND user_id = :user_id
                                    ');
            // Exécute la requête avec les nouvelles données
            $success = $updateRequest->execute([
                'title' => $title,
                'duration' => $duration,
                'date' => $date,
                'image' => $uniqueFileName, // Nom unique du fichier
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
    <form action="modify.php?id=<?= $film_id ?>" method="post" enctype="multipart/form-data">
        <label for="title">Modifier le titre :</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($film_data['title'] ?? '') ?>" required>
        <label for="duration">Modifier la durée du film en minutes :</label>
        <input type="number" id="duration" name="duration" value ="<?= htmlspecialchars($film_data['duration'] ?? '') ?>" required>
        <label for="date">Modifier la date de sortie du film :</label>
        <input type="number" id="date" name="date" value="<?= htmlspecialchars($film_data['date'] ?? '') ?>" required> 
        <label for="image">Modifier l'image du film : <img src="images/<?=$film_data['image']?>" alt="Image du film"></label>
        <input type="file" id="image" name="image" value="<?= $film_data['image'] ?? '' ?>"> 
        <button type="submit">Modifier</button>
    </form>
    <a href="index.php">Retour à la liste des films</a>
</body>
</html>