<?php 
    session_start();
    $bdd = new PDO('mysql:host=localhost;dbname=film;charset=utf8', 'root', 'root');

    $image = null;

    if (isset($_FILES['image'])) {
        $fileName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];
        $location = 'images/';

        $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        var_dump($_FILES['image']);
        if ($imageFileType != "jpg" && $imageFileType !='png' && $imageFileType != 'jpeg' && $imageFileType != "gif") {
            echo 'Veuillez choisir une image au format JPG, JPEG, PNG ou GIF';
        } else {
            $uniqueFileName = uniqid('Image_', true) . '.' . $imageFileType;

            
            if (move_uploaded_file($tmpName, $location.$uniqueFileName)) {
                echo "L'image a été téléchargée";
                $image = $location.$uniqueFileName;
            } else {
                echo "Erreur lors du téléchargement";
                $image = null;
            }
        }
    }

    if (isset($_POST['title']) && isset($_POST['duration']) && isset($_POST['date'])) {
        $title = htmlspecialchars($_POST['title']);
        $duration = htmlspecialchars($_POST['duration']);
        $date = htmlspecialchars($_POST['date']);

        $request = $bdd->prepare('  INSERT INTO fiche_film (title,duration,date, image)
                                     VALUES (:title,:duration,:date,:image)
                                ');
        $request->execute([
            'title' =>$title,
            'duration' =>$duration,
            'date' =>$date,
            'image' =>$image,
        ]);
        echo "Film ajouté !";
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