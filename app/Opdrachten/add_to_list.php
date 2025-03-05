<?php
    include 'classes/User.php';

    session_start();

    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    $userId = $_SESSION['user']->getId();
    $gameId = $_POST['game_id'];

    $db = new Database();
    $gameManager = new GameManager($db);

    try {
        $gameManager->addToUserGames($userId, $gameId);
        header("Location: user_list.php");
    } catch (Exception $e) {
        echo "<div>Error: " . $e->getMessage() . "</div>";
    }
    exit();
?>