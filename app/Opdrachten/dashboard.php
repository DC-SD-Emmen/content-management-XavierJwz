<?php
    session_start();

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
        <div class="nav-item"><a href="logout.php">Logout</a></div>
    </nav>

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
       
        <div class=gameGrid>
            <?php foreach ($games as $game): ?>
                <div>
                    <a href="game_details.php?game_id=<?php echo $game->getID(); ?>">
                        <img src="uploads/<?php echo htmlspecialchars($game->get_image()); ?>" alt="<?php echo htmlspecialchars($game->get_title()); ?>" class="gameImage">
                    </a>
                    <form method="post" action="add_to_list.php">
                        <input type="hidden" name="game_id" value="<?php echo $game->getID(); ?>">
                        <input type="submit" id="addGameButton" value="+ Add Game To List">
                    </form> 
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script type='text/javascript' src='script.js'></script>
</body>
</html>