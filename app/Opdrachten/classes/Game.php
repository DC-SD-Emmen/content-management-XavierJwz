<?php
    class Game{
      
      private $id;
      private $title;
      private $developer;
      private $description;
      private $genre;
      private $platform;
      private $releaseyear;
      private $rating;
      private $image;

      public function setID($id) {
        $this->id = $id;
      }

      public function getID() {
        return $this->id;
      }

      function set_title($title) {
        $this->title = $title;
      }

      function get_title() {
        return $this->title;
      }

      function set_description($description) {
        $this->description = $description;
      }

      function get_description() {
        return $this->description;
      }

      function set_developer($developer) {
        $this->developer = $developer;
      }

      function get_developer() {
        return $this->developer;
      }

      function set_genre($genre) {
        $this->genre = $genre;
      }

      function get_genre() {
        return $this->genre;
      }

      function set_platform($platform) {
        $this->platform = $platform;
      }

      function get_platform() {
        return $this->platform;
      }

      function set_releaseyear($releaseyear) {
        $this->releaseyear = $releaseyear;
      }

      function get_releaseyear() {
        return $this->releaseyear;
      }

      function set_rating($rating) {
        $this->rating = $rating;
      }

      function get_rating() {
        return $this->rating;
      }

      function set_image($image) {
        $this->image = $image;
      }

      function get_image() {
        return $this->image;
      }

      

  }
    
  
?>