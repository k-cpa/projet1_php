<?php
    // Démarrage de la session
    // Nécessaire pour pouvoir manipuler les données de session, même si c'est pour les détruire
    session_start();

    // Supprime toutes les variables de session
    // Cela vide les données stockées dans `$_SESSION` sans fermer la session elle-même
    session_unset();

    // Détruit complètement la session
    // Cela supprime la session côté serveur (par exemple, le fichier temporaire lié à cette session)
    session_destroy();

    // Redirige l'utilisateur vers la page d'accueil après la déconnexion
    // La fonction `header()` modifie l'emplacement de la requête HTTP pour renvoyer vers une autre URL
    header('location:index.php');

    // Interrompt l'exécution du script après la redirection
    // Cela garantit que rien d'autre ne sera envoyé au client après la redirection
    exit;
?>
