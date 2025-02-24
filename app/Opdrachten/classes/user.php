<?php
    class User {
        private $id;
        private $username;

        public function __construct($username, $id = null) {
            $this->id = $id;
            $this->username = $username;
        }

        public function getId() {
            return $this->id;
        }

        public function getUsername() {
            return $this->username;
        }

    }
?>