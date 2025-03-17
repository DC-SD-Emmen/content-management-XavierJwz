<?php
    // Registreer een autoloader om klassen automatisch te laden uit de 'classes' map
    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    // Start de sessie om toegang te krijgen tot sessievariabelen
    session_start();

    // Controleer of de gebruiker is ingelogd door te kijken naar de 'user' sessievariabele
    if (!isset($_SESSION['user'])) {
        // Als de gebruiker niet is ingelogd, stuur door naar de inlogpagina
        header("Location: login.php");
        exit();
    }

    // Initialiseer de database en de userManager objecten
    $db = new Database();
    $userManager = new UserManager($db);

    // Verkrijg de momenteel ingelogde gebruiker uit de sessie
    $user = $_SESSION['user'];

    // Initialiseer de gameManager en haal het ID van het eerste spel op
    $gameManager = new GameManager($db);
    $firstGameId = $gameManager->fetch_first_game_id();

    // Verwerk formulierindieningen via de POST-methode
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Controleer of het formulier voor bijwerken van gebruikersgegevens is ingediend
        if (isset($_POST['update'])) {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            try {
                // Probeer de gebruikersgegevens bij te werken in de database
                $userManager->updateUser($user->getId(), $username, $email, $password);
                // Bijwerken van sessiegegevens na succesvolle update
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                echo "Account succesvol bijgewerkt.";
            } catch (Exception $e) {
                // Toon een foutmelding als er een fout optreedt
                echo "Fout: " . $e->getMessage();
            }
        } 
        // Controleer of het formulier voor het verwijderen van de gebruiker is ingediend
        elseif (isset($_POST['delete'])) {
            try {
                // Probeer de gebruiker te verwijderen uit de database
                $userManager->deleteUser($user->getId());
                // Verwijder de sessie en stuur door naar de inlogpagina
                session_unset();
                session_destroy();
                header("Location: login.php");
                exit();
            } catch (Exception $e) {
                // Toon een foutmelding als er een fout optreedt
                echo "Fout: " . $e->getMessage();
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Xavier's Game Library</title>
        <link rel="stylesheet" href="stylegl.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    </head>
    <body class="background">
        <div>
            <nav class="navbar">
                <div class="nav-item"><a href="dashboard.php">Library</a></div>
                <div class="nav-item"> 
                    <?php if ($firstGameId): ?>
                        <a href="game_details.php?game_id=<?php echo $firstGameId; ?>">Game Details</a>
                    <?php endif; ?>
                </div>  
                <div class="nav-item"><a href="user_list.php">My List</a></div>
                <div class="nav-item active"><a href="usermanagement.php">Account</a></div>
                <div class="nav-item"><a href="logout.php">Logout</a></div>
            </nav>
        </div>
        
        <div class="umbody">
            <h2>Update Account Information</h2>
            <form method="POST" action="usermanagement.php">
                <div class="umform">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" placeholder="Enter new username" required>
                </div>
                <div class="umform">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" placeholder="Enter new email" required>
                </div>
                <div class="umform">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" value="" placeholder="Enter new password">
                </div>
                <div>
                    <button type="submit" name="update" id="GameButton">Update</button>
                </div>
            </form>
            <form method="POST" action="usermanagement.php">
                <div>
                    <button type="submit" name="delete" id="GameButton">Delete Account</button>
                </div>
            </form>
        </div>
        
    </body>
</html>