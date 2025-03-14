<?php
    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    $db = new Database();
    $userManager = new UserManager($db);
    $user = $_SESSION['user'];

    $gameManager = new GameManager($db);
    $firstGameId = $gameManager->fetch_first_game_id();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            $userManager->updateUser($user->getId(), $username, $email, $password);
            // echo "Account updated successfully.";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
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
                    <button type="submit" id="addGameButton">Update</button>
                </div>
            </form>
        </div>
        
    </body>
</html>