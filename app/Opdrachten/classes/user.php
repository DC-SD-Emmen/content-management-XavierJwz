<?php
    class User {
        private $id;
        private $username;
        private $email;

        public function __construct($username, $email, $id = null) {
            $this->id = $id;
            $this->username = $username;
            $this->email = $email;
        }

        public function getId() {
            return $this->id;
        }

        public function getUsername() {
            return $this->username;
        }

        public function getEmail() {
            return $this->email;
        }

        public static function fromArray($data) {
            return new self($data['username'], $data['email'], $data['id']);
        }
    }
?>