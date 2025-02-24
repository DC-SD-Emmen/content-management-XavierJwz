<?php

$host = "mysql"; // Le host est le nom du service, présent dans le docker-compose.yml
$dbname = "my-wonderful-website";
$charset = "utf8";
$port = "3306";
?>

<?php 

    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    $db = new Database();

    $gameManager = new GameManager($db);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        try {
            $gameManager->fileUpload($_FILES['image']);
            $gameManager->insertData($_POST, $_FILES['image']['name']);
        } catch (Exception $e) {
            echo "<div>Unexpected error during form submission: " . $e->getMessage() . "</div>";
        }
    }

    $games = $gameManager->fetch_all_games();

    $firstGameId = $gameManager->fetch_first_game_id();

?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xavier's Game Library</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="nav-item active"><a href="index.php">Library</a></div>
        <div class="nav-item"> 
            <?php if ($firstGameId): ?>
                <a href="game_details.php?game_id=<?php echo $firstGameId; ?>">Game Details</a>
                <?php else: ?>
            <?php endif; ?>
        </div> 
        <div class="nav-item"><button id='add-game'>Add Game</button></div>   
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
                </div>
            <?php endforeach; ?>
        </div>

        <div class="addGame" id='game-form'>

            <form method="POST" enctype="multipart/form-data">

                <div >
                    <label for='title'> Title <br></label>
                    <input type="text" id="text" name="title" size="75" required>
                </div>

                <div>
                    <label for='developer'> Developer <br></label>
                    <input type="text" id="text" name="developer" size="75" required>
                </div>

                <div>
                    <label for='genre'> Genre <br></label>
                    <input type="text" id="text" name="genre" size="75" required>
                </div>

                <div>
                    <label for='releaseyear'> Release <br></label>
                    <input type="date" name="releaseyear" id="date" required>
                </div>

                <div>
                    <label for='platform'> Platform <br></label>
                    <input type="text" id="text" name="platform" size="75" required>
                </div>

                <div>
                    <label for='description'> Description <br></label>
                    <textarea name="description" rows="10" cols="75" required
                    ></textarea>
                </div>

                <div>
                    <label for='rating'> Rating <br></label>
                    <input type="range" id="rating" name="rating" min="1.0" max="10.0" step="0.1" value="1.0"
                        oninput="this.nextElementSibling.value = parseFloat(this.value).toFixed(1)">
                    <output for="rating">1.0</output>
                </div>

                <div>
                    <label for='image'> Image <br></label>
                    <input type="file" name="image" id="fileToUpload">
                </div>

                <input type="submit" name='submit' id="submit">

            </form>

        </div>

    </div>


    <script type='text/javascript' src='script.js'></script>
</body>
</html>