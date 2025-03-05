<?php
    class usermanager {
        private $conn;

        public function __construct(Database $db) {
            $this->conn = $db->getConnection();
        }

        public function validateInput($username, $password) {
            $usernamePattern = '/^[a-zA-Z0-9_]{3,30}$/';
            $passwordPattern = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/';
            $emailPattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

            if (!preg_match($usernamePattern, $username)) {
                throw new Exception("Invalid username format.");
            }

            if (!preg_match($passwordPattern, $password)) {
                throw new Exception("Invalid password format.");
            }
        }

        public function registerUser($username, $password, $email) {
            $this->validateInput($username, $password);
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format.");
            }
        
            // Check if username or email already exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();
        
            if ($count > 0) {
                throw new Exception("Username or email already exists.");
            }
        
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
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