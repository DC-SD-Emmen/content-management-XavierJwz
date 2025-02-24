<?php
    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }


    $game_id = isset($_GET['game_id']) ? $_GET['game_id'] : '';

    $db = new Database();
    $gameManager = new GameManager($db);

    $singleGame = $gameManager->fetch_game_by_title($game_id);

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
        echo "<div>Game not found.</div>";
    }
?>