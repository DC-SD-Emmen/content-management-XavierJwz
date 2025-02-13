<?php
include 'classes/User.php';

session_start();

spl_autoload_register(function ($class_name) {
    include 'classes/' . $class_name . '.php';
});

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <h1>Dashboard</h1>
    <p>Welcome to your dashboard, <?php echo $_SESSION['user']->getUsername(); ?>!</p>
    <a href="logout.php"><button>Logout</button></a>
</body>
</html>