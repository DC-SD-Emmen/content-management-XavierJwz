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

$db = new Database();
$gameManager = new GameManager($db);

$userGames = $gameManager->fetchUserGames($userId);
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
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>