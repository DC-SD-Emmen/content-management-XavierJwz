<?php
    // Start de sessie om toegang te krijgen tot sessiegegevens
    session_start();

    // Verwijder alle sessievariabelen
    session_unset();

    // Vernietig de sessie volledig
    session_destroy();

    // Stuur de gebruiker terug naar de loginpagina
    header("Location: login.php");
    exit();
?>
