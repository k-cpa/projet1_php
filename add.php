<?php 
session_start(); // Démarre une session PHP pour gérer les variables de session

// Connexion à la base de données via PDO
$bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');

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

    // Déplace le fichier depuis le chemin temporaire vers le dossier cible
    if (move_uploaded_file($tmpName, $image)) {
        // Si le téléchargement réussit, affiche une balise <img> pour prévisualiser l'image
        echo "<img src='" . $image . "' alt='Image du film'>";
    } else {
        // Affiche un message si le déplacement échoue
        echo "Erreur lors du téléchargement";
    }
}

// Vérifie si les champs `title`, `duration` et `date` ont été envoyés via le formulaire
if (isset($_POST['title']) && isset($_POST['duration']) && isset($_POST['date'])) {
    // Récupère et sécurise les valeurs soumises par l'utilisateur
    $title = htmlspecialchars($_POST['title']); // Supprime les caractères spéciaux pour éviter les injections HTML
    $duration = htmlspecialchars($_POST['duration']);
    $date = htmlspecialchars($_POST['date']);
    $user_id = $_SESSION['id'];
    
    // Prépare une requête SQL pour insérer les données dans la base
    $request = $bdd->prepare('
        INSERT INTO fiche_film (title, duration, date, image, user_id)
        VALUES (:title, :duration, :date, :image, :user_id)
    ');

    // Exécute la requête avec les données soumises par l'utilisateur
    $request->execute([
        'title' => $title, // Titre du film
        'duration' => $duration, // Durée du film
        'date' => $date, // Date de sortie du film
        'image' => $uniqueFileName, // Nom unique du fichier image
        'user_id' => $user_id // User ID utilisé lors de la création
    ]);
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Ajouter un film</title>
</head>
<body>
    <?php include('nav.php');?>
    
    <form action="add.php" method="POST" enctype="multipart/form-data">
        <label for="title">Le titre de votre film :</label>
        <input type="text" id="title" name="title" required>
        <label for="duration">La durée de votre film :</label>
        <input type="text" id="duration" name="duration" required>
        <label for="date">L'année de sortie du film :</label>
        <input type="text" id="date" name="date" required> 
        <label for="image">Image du film - optionnel</label>
        <input type="file" id="image" name ="image">
        <button>Enregistrer</button>
    </form>
</body>
</html>