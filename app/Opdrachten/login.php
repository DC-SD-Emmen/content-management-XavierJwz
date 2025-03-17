<?php
    // Start een sessie om gebruikersgegevens op te slaan
    session_start();

    // Autoload-functie om automatisch klassen te laden vanuit de "classes" map
    spl_autoload_register(function ($class_name) {
        include 'classes/' . $class_name . '.php';
    });

    // Maak een nieuwe databaseverbinding en een UserManager-object
    $database = new Database();
    $userManager = new usermanager($database);

    // Variabele voor eventuele foutmeldingen of berichten
    $message = '';

    // Controleer of het formulier is ingediend via een POST-verzoek
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verkrijg de gebruikersnaam en wachtwoord uit het formulier en ontsmet de invoer
        $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

        try {
            // Probeer de gebruiker in te loggen
            $user = $userManager->loginUser($username, $password);
            
            // Beveilig de sessie door een nieuw sessie-ID te genereren
            session_regenerate_id(true);
            
            // Sla de ingelogde gebruiker op in de sessie
            $_SESSION['user'] = $user;

            // Stuur de gebruiker door naar de dashboardpagina
            header("Location: dashboard.php");
            exit();
        } catch (Exception $e) {
            // Als inloggen mislukt, sla het foutbericht op
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