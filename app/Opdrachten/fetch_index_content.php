<?php
    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    $db = new Database();
    $gameManager = new GameManager($db);

    $games = $gameManager->fetch_all_games();

    foreach ($games as $game) {
        echo "<div>";
        echo "<a href='game_details.php?game_id=" . $game->getID() . "'>";
        echo "<img src='uploads/" . htmlspecialchars($game->get_image()) . "' alt='" . htmlspecialchars($game->get_title()) . "' class='gameImage'>";
        echo "</a>";
        echo "</div>";
    }
?>