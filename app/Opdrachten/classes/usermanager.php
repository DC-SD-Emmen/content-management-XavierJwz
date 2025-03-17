<?php
// Klasse voor het beheren van gebruikers (registratie, login, update, verwijdering)
    class UserManager {
        private $conn;

        // Constructor om de databaseverbinding in te stellen
        public function __construct(Database $db) {
            $this->conn = $db->getConnection();
        }

        // Validatie van gebruikersinvoer (gebruikersnaam, wachtwoord en e-mail)
        public function validateInput($username, $password) {
            $usernamePattern = '/^[a-zA-Z0-9_]{3,30}$/';
            $passwordPattern = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/';
            $emailPattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

            // Controleer of de gebruikersnaam voldoet aan het patroon
            if (!preg_match($usernamePattern, $username)) {
                throw new Exception("Invalid username format.");
            }

            // Controleer of het wachtwoord voldoet aan het patroon
            if (!preg_match($passwordPattern, $password)) {
                throw new Exception("Invalid password format.");
            }
        }

        // Gebruiker registreren
        public function registerUser($username, $password, $email) {
            $this->validateInput($username, $password);
            
            // E-mailvalidering
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format.");
            }
        
            // Controleer of de gebruikersnaam of e-mail al bestaat in de database
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();
        
            if ($count > 0) {
                throw new Exception("Username or email already exists.");
            }
        
            // Versleutel het wachtwoord
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
            // Voer de registratie in de database uit
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        
            if ($stmt->execute()) {
                return "User registered successfully.";
            } else {
                return "Error registering user.";
            }
        }

        // Gebruiker inloggen
        public function loginUser($username, $password) {
            $this->validateInput($username, $password);
        
            // Zoek de gebruiker in de database op gebruikersnaam
            $stmt = $this->conn->prepare("SELECT id, username, password, email FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
        
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$result) {
                throw new Exception("Invalid username.");
            }
        
            // Vergelijk het wachtwoord met het versleutelde wachtwoord in de database
            if (!password_verify($password, $result['password'])) {
                throw new Exception("Invalid password.");
            }
        
            // Maak een gebruikersobject aan
            $user = new User($result['username'], $result['email'], $result['id']);

            // Zet gebruikersgegevens in de sessie
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['email'] = $user->getEmail();
        
            return $user;
        }

        // Gebruikersgegevens bijwerken
        public function updateUser($id, $username, $email, $password = null) {
            // Valideer de invoer
            $this->validateInput($username, $password);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format.");
            }

            // Controleer of de gebruikersnaam of e-mail al bestaat voor andere gebruikers
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE (username = :username OR email = :email) AND id != :id");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                throw new Exception("Username or email already exists.");
            }

            // Bijwerken van gebruikersinformatie
            if ($password) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $this->conn->prepare("UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id");
                $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            } else {
                $stmt = $this->conn->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
            }
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return "User updated successfully.";
            } else {
                throw new Exception("Error updating user.");
            }
        }

        // Gebruiker verwijderen
        public function deleteUser($id) {
            // Verwijder eerst de gekoppelde spelgegevens
            $stmt = $this->conn->prepare("DELETE FROM user_games WHERE user_id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verwijder de gebruiker uit de gebruikersdatabase
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return "User deleted successfully.";
            } else {
                throw new Exception("Error deleting user.");
            }
        }
    }
?>
