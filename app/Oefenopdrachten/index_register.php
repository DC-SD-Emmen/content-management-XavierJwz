<?php
    $host = "mysql"; 
    $dbname = "my-wonderful-website";
    $charset = "utf8";
    $port = "3306";

    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    $database = new Database();
    $userManager = new user_manager($database);

    $message = '';
    $success = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

        try {
            $message = $userManager->registerUser($username, $password);
            if ($message === "User registered successfully.") {
                $success = true;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
    }
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP Registration Page</title>
        <link rel="stylesheet" href="style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
        <script>
            function redirectToLogin() {
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            }
        </script>
    </head>

    <body>
        <h1>PHP Registration Page</h1>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Register">
        </form>

        <button><a href="index.php">Go Back</a></button>
        
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
            <?php if ($success): ?>
                <script>redirectToLogin();</script>
            <?php endif; ?>
        <?php endif; ?>
    </body>
</html>