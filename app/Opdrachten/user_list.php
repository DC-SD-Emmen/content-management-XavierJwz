<?php
    // Inclusie van de User klasse
    include 'classes/User.php';

    // Start de sessie om gebruikersinformatie te behouden
    session_start();

    // Automatisch laden van klassen uit de 'classes' map
    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    // Controleer of de gebruiker is ingelogd, anders omleiden naar de loginpagina
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    // Haal de gebruikers-ID op uit de sessie
    $userId = $_SESSION['user']->getId();

    // Initialisatie van databaseverbinding en game manager
    $db = new Database();
    $gameManager = new GameManager($db);

    // Haal de games op die gekoppeld zijn aan de ingelogde gebruiker
    $userGames = $gameManager->fetchUserGames($userId);

    // Haal het ID op van de eerste game in de database
    $firstGameId = $gameManager->fetch_first_game_id();
?>


<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Game List</title>
    <link rel="stylesheet" href="stylegl.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-item"><a href="dashboard.php">Library</a></div>
        <div class="nav-item"> 
            <?php if ($firstGameId): ?>
                <a href="game_details.php?game_id=<?php echo $firstGameId; ?>">Game Details</a>
            <?php endif; ?>
        </div>  
        <div class="nav-item active"><a href="user_list.php">My List</a></div>
        <div class="nav-item"><a href="usermanagement.php">Account</a></div>
        <div class="nav-item"><a href="logout.php">Logout</a></div>
    </nav>
    <div class="library">
        <div class="gameGrid listGrid">
            <?php if (empty($userGames)): ?>
                <p>No games in your list.</p>
            <?php else: ?>
                <?php foreach ($userGames as $game): ?>
                    <div>
                        <a href="game_details.php?game_id=<?php echo $game['id']; ?>">
                            <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>" class="gameImage">
                        </a>
                        <p><?php echo htmlspecialchars($game['title']); ?></p>
                        <form method="POST" action="add_to_list.php">
                            <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
                            <button type="submit" name="remove" id="GameButton">Remove from List</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>