<?php
    class user_manager {
        private $conn;

        public function __construct(Database $db) {
            $this->conn = $db->getConnection();
        }

        public function validateInput($username, $password) {
            $usernamePattern = '/^[a-zA-Z0-9_]{3,30}$/';
            $passwordPattern = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/';

            if (!preg_match($usernamePattern, $username)) {
                throw new Exception("Invalid username format.");
            }

            if (!preg_match($passwordPattern, $password)) {
                throw new Exception("Invalid password format.");
            }
        }

        public function registerUser($username, $password) {
            $this->validateInput($username, $password);
        
            // Check if username already exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();
        
            if ($count > 0) {
                throw new Exception("Username already exists.");
            }
        
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
            $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        
            if ($stmt->execute()) {
                return "User registered successfully.";
            } else {
                return "Error registering user.";
            }
        }

        public function loginUser($username, $password) {
            $this->validateInput($username, $password);
        
            $stmt = $this->conn->prepare("SELECT id, username, password FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
        
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$result) {
                throw new Exception("Invalid username.");
            }
        
            if (!password_verify($password, $result['password'])) {
                throw new Exception("Invalid password.");
            }
        
            return new User($result['username'], $result['id']);
        }
}
?>