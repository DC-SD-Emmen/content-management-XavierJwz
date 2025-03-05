<?php
    session_start();

    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });
    

    $database = new Database();
    $userManager = new usermanager($database);

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
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("password");
            var toggleButton = document.getElementById("togglePassword");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleButton.textContent = "Hide Password";
            } else {
                passwordField.type = "password";
                toggleButton.textContent = "Show Password";
            }
        }
    </script>
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
                <button type="button" id="togglePassword" onclick="togglePasswordVisibility()">Show Password</button><br><br>
                <input type="submit" value="Login">
            </form>

            <a href="register.php"><button>Don't have an account yet? <br> Register now!</button></a>
        <?php endif; ?>

        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>

</body>
</html>