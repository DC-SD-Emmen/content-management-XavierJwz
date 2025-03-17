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

    // Maak een nieuwe databaseverbinding
    $db = new Database();
    
    // Maak een nieuwe GameManager instantie voor gamebeheer
    $gameManager = new GameManager($db);

    // Haal alle games op uit de database
    $games = $gameManager->fetch_all_games();

    // Loop door alle opgehaalde games en toon ze in de interface
    foreach ($games as $game) {
        echo "<div>";
        // Link naar de game details pagina met de juiste game-ID
        echo "<a href='game_details.php?game_id=" . $game->getID() . "'>";
        // Toon de game-afbeelding met beveiligde output
        echo "<img src='uploads/" . htmlspecialchars($game->get_image()) . "' alt='" . htmlspecialchars($game->get_title()) . "' class='gameImage'>";
        echo "</a>";
        echo "</div>";
    }
?>
