<?php
    // Inclusion van de User class om gebruikersinformatie te beheren
    include 'classes/User.php';

    // Start een sessie om gebruikersgegevens op te slaan
    session_start();

    // Autoload functies om automatisch klassen te laden uit de "classes" map
    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    // Controleer of de gebruiker is ingelogd, zo niet, stuur door naar login pagina
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    // Haal de ingelogde user ID op uit de sessie
    $userId = $_SESSION['user']->getId();
    // Haal de game-ID op uit het POST-verzoek
    $gameId = $_POST['game_id'];

    // Maak een nieuwe databaseverbinding en een GameManager instantie
    $db = new Database();
    $gameManager = new GameManager($db);

    try {
        // Controleer of de gebruiker een game wil toevoegen of verwijderen
        if (isset($_POST['add'])) {
            if ($gameManager->isGameInUserGames($userId, $gameId)) {
                $_SESSION['message'] = "Game is already in your wishlist.";
            } else {
                $gameManager->addToUserGames($userId, $gameId);
                $_SESSION['message'] = "Game added to your wishlist.";
            }
        } elseif (isset($_POST['remove'])) {
            $gameManager->removeFromUserGames($userId, $gameId);
            $_SESSION['message'] = "Game removed from your wishlist.";
        }
        // Stuur gebruiker na de actie terug naar de vorige pagina
        $referer = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
        header("Location: $referer");
    } catch (Exception $e) {
        // Toon een foutmelding als er een uitzondering optreedt
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $referer = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
        header("Location: $referer");
    }
    exit();
?>