<?php
    // Start een sessie om gebruikersgegevens op te slaan
    session_start();

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

    // Haal de ID van de eerste game op (bijvoorbeeld voor navigatie)
    $firstGameId = $gameManager->fetch_first_game_id();
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xavier's Game Library</title>
    <link rel="stylesheet" href="stylegl.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-item active"><a href="dashboard.php">Library</a></div>
        <div class="nav-item"> 
            <?php if ($firstGameId): ?>
                <a href="game_details.php?game_id=<?php echo $firstGameId; ?>">Game Details</a>
            <?php endif; ?>
        </div>  
        <div class="nav-item"><a href="user_list.php">My List</a></div>
        <div class="nav-item"><a href="usermanagement.php">Account</a></div>
        <div class="nav-item"><a href="logout.php">Logout</a></div>
    </nav>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php echo $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div class="library">
        <div class="gameSidebar">
            <?php foreach ($games as $game): ?>
                <div class="gameSidebarItem">
                    <a href="game_details.php?game_id=<?php echo $game->getID(); ?>">
                        <img src="uploads/<?php echo htmlspecialchars($game->get_image()); ?>" alt="<?php echo htmlspecialchars($game->get_title()); ?>" class="sidebarGameImage">
                        <span class="gameTitle"><?php echo htmlspecialchars($game->get_title()); ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
       
        <div class="gameGrid">
            <?php foreach ($games as $game): ?>
                <div>
                    <a href="game_details.php?game_id=<?php echo $game->getID(); ?>">
                        <img src="uploads/<?php echo htmlspecialchars($game->get_image()); ?>" alt="<?php echo htmlspecialchars($game->get_title()); ?>" class="gameImage">
                    </a>
                    <form method="POST" action="add_to_list.php">
                        <input type="hidden" name="game_id" value="<?php echo $game->getID(); ?>">
                        <button type="submit" name="add" id="GameButton">Add to List</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script type='text/javascript' src='script.js'></script>
    <script type="text/javascript">
        setTimeout(function() {
            var messageDiv = document.querySelector('.message');
            if (messageDiv) {
                messageDiv.style.display = 'none';
            }
        }, 2000);
    </script>
</body>
</html>