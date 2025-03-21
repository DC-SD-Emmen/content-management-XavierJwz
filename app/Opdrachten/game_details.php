<?php
    // Start een sessie om gebruikersgegevens op te slaan
    session_start();

    // Controleer of de gebruiker is ingelogd, zo niet, stuur door naar de loginpagina
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    // Autoload functie om automatisch klassen te laden vanuit de "classes" map
    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    // Haal de game-ID op uit de URL, als deze is ingesteld
    $game_id = isset($_GET['game_id']) ? $_GET['game_id'] : '';

    // Maak een nieuwe databaseverbinding
    $db = new Database();

    // Maak een nieuwe GameManager instantie voor gamebeheer
    $gameManager = new GameManager($db);
    
    // Controleer of het formulier is ingediend via een POST-verzoek
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            // Upload het bestand en voeg de gegevens toe aan de database
            $gameManager->fileUpload($_FILES['image']);
            $gameManager->insertData($_POST, $_FILES['image']['name']);
        } catch (Exception $e) {
            // Toon een foutmelding als er een onverwachte fout optreedt
            echo "<div>Unexpected error during form submission: " . $e->getMessage() . "</div>";
        }
    }

    // Haal gegevens van een enkele game op aan de hand van de game-ID
    $singleGame = $gameManager->fetch_game_by_title($game_id);  

    // Haal alle beschikbare games op
    $games = $gameManager->fetch_all_games();

    // Controleer of de opgezochte game bestaat
    if (!$singleGame) {
        echo "<div>Game not found.</div>";
        exit;
    }

    // Haal de ID op van de eerste game in de database
    $firstGameId = $gameManager->fetch_first_game_id();
?>


<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($singleGame->get_title()); ?> - Game Details</title>
    <link rel="stylesheet" href="stylegl.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-item"><a href="dashboard.php">Library</a></div>
        <div class="nav-item active"> 
            <?php if ($firstGameId): ?>
                <a href="game_details.php?game_id=<?php echo $firstGameId; ?>">Game Details</a>
            <?php endif; ?>
        </div> 
        <div class="nav-item"><a href="user_list.php">My List</a></div>
        <div class="nav-item"><a href="usermanagement.php">Account</a></div>
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
        <div class="gameDetails">
            <h1><strong><?php echo htmlspecialchars($singleGame->get_title()); ?></strong></h1>
            <img src="uploads/<?php echo htmlspecialchars($singleGame->get_image()); ?>" alt="<?php echo htmlspecialchars($game->get_title()); ?>" class="gameImage">
            <p><strong>Developer:</strong> <?php echo htmlspecialchars($singleGame->get_developer()); ?></p>
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($singleGame->get_genre()); ?></p>
            <p><strong>Platform:</strong> <?php echo htmlspecialchars($singleGame->get_platform()); ?></p>
            <p><strong>Release Year:</strong> <?php echo date("d/m/Y", strtotime($singleGame->get_releaseyear())); ?></p>
            <p><strong>Rating:</strong> <?php echo htmlspecialchars($singleGame->get_rating()); ?></p>
            <p><strong>Description:<br></strong> <?php echo nl2br(htmlspecialchars($singleGame->get_description())); ?></p>
        </div>
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