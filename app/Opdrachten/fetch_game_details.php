<?php
    // Autoload functie om automatisch klassen te laden uit de "classes" map
    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    // Controleer of de gebruiker is ingelogd, zo niet, stuur door naar de loginpagina
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    // Haal de game-ID op uit de URL, als deze is ingesteld
    $game_id = isset($_GET['game_id']) ? $_GET['game_id'] : '';

    // Maak een nieuwe databaseverbinding
    $db = new Database();
    
    // Maak een nieuwe GameManager instantie voor gamebeheer
    $gameManager = new GameManager($db);

    // Haal de game op uit de database op basis van de titel
    $singleGame = $gameManager->fetch_game_by_title($game_id);

    // Controleer of de game is gevonden en toon de details
    if ($singleGame) {
        echo "<h1><strong>" . htmlspecialchars($singleGame->get_title()) . "</strong></h1>";
        echo "<img src='uploads/" . htmlspecialchars($singleGame->get_image()) . "' alt='" . htmlspecialchars($singleGame->get_title()) . "' class='gameImage'>";
        echo "<p><strong>Developer:</strong> " . htmlspecialchars($singleGame->get_developer()) . "</p>";
        echo "<p><strong>Genre:</strong> " . htmlspecialchars($singleGame->get_genre()) . "</p>";
        echo "<p><strong>Platform:</strong> " . htmlspecialchars($singleGame->get_platform()) . "</p>";
        echo "<p><strong>Release Year:</strong> " . date("d/m/Y", strtotime($singleGame->get_releaseyear())) . "</p>";
        echo "<p><strong>Rating:</strong> " . htmlspecialchars($singleGame->get_rating()) . "</p>";
        echo "<p><strong>Description:<br></strong> " . nl2br(htmlspecialchars($singleGame->get_description())) . "</p>";
    } else {
        // Toon een foutmelding als de game niet is gevonden
        echo "<div>Game not found.</div>";
    }
?>
