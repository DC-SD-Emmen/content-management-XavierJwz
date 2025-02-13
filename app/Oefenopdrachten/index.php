<?php
    include 'classes/User.php';

    session_start();

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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

        try {
            $user = $userManager->loginUser($username, $password);
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            header("Location: dashboard.php");
            exit();
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
    }
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Login Page</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>PHP Login Page</h1>

        <?php if (isset($_SESSION['user'])): ?>
            <p>Welcome, <?php echo $_SESSION['user']->getUsername(); ?>!</p>
            <button><a href="logout.php">Logout</a></button>
        <?php else: ?>
            <form method="post">
                <label for="username">Username:<br></label>
                <input type="text" id="username" name="username" required><br><br>
                <label for="password">Password:<br></label>
                <input type="password" id="password" name="password" required><br><br>
                <input type="submit" value="Login">
            </form>

            <a href="index_register.php"><button>Don't have an account yet? <br> Register now!</button></a>
        <?php endif; ?>

        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>

</body>
</html>