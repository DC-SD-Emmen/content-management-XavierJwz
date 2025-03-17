<?php
    class GameManager {
        private $conn;

        public function __construct(Database $db) {
            $this->conn = $db->getConnection();
        }

        public function insertData($data, $image) {
            if (!$this->validateData($data)) {
                echo "<div>Validation failed. Please check your input values.</div>";
                return; 
            }

            $title = htmlspecialchars($data['title']);
            $developer = htmlspecialchars($data['developer']);
            $description = htmlspecialchars($data['description']);
            $genre = htmlspecialchars($data['genre']);
            $platform = htmlspecialchars($data['platform']);
            $releaseyear = htmlspecialchars($data['releaseyear']);
            $rating = htmlspecialchars($data['rating']);
            
            try {
                $sql = "INSERT INTO games (title, developer, description, genre, platform, releaseyear, rating, image)
                        VALUES (:title, :developer, :description, :genre, :platform, :releaseyear, :rating, :image)";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':developer', $developer);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':genre', $genre);
                $stmt->bindParam(':platform', $platform);
                $stmt->bindParam(':releaseyear', $releaseyear);
                $stmt->bindParam(':rating', $rating);
                $stmt->bindParam(':image', $image);
                $stmt->execute();
                echo "<div>Game successfully inserted!</div>";

            } catch (PDOException $e) {
                echo "<div>Data insert failed: " . $e->getMessage() . "</div>";
                if (isset($stmt)) {
                    echo "<div>SQL Error: " . implode(", ", $stmt->errorInfo()) . "</div>";
                }
            } catch (Exception $e) {
                echo "<div>An unexpected error occurred: " . $e->getMessage() . "</div>";
            }
        }

        private function validateData($data) {
            $titleregex = '/^[a-zA-Z0-9\s:,\-!()]+$/';
            $developerregex = '/^[a-zA-Z\s\-\.\',]+$/';
            $genreregex = '/^[a-zA-Z\s\/\-]+$/';
            $platformregex = '/^[a-zA-Z0-9\s\+\-]+$/';
            $releaseyearegex = '/^(1959|19[6-9]\d|20\d{2})-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/';
            $ratingregex = '/^([1-9](\.\d)?|10(\.0)?)$/';

            if (!preg_match($titleregex, $data['title'])) {
                echo "<div>Title is invalid.</div>";
                return false;
            }

            if (!preg_match($developerregex, $data['developer'])) {
                echo "<div>Developer is invalid.</div>";
                return false;
            }

            if (!preg_match($genreregex, $data['genre'])) {
                echo "<div>Genre is invalid.</div>";
                return false;
            }

            if (!preg_match($platformregex, $data['platform'])) {
                echo "<div>Platform is invalid.</div>";
                return false;
            }

            if (!preg_match($releaseyearegex, $data['releaseyear'])) {
                echo "<div>Release date is invalid.</div>";
                return false;
            }

            if (!preg_match($ratingregex, $data['rating'])) {
                echo "<div>Rating is invalid.</div>";
                return false;
            }

            return true;
        }

        public function fileUpload($file) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($file["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is an actual image or fake image
            $check = getimagesize($file["tmp_name"]);
            if ($check !== false) {
                // Image is valid
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            // Check if file already exists
            if (file_exists($target_file)) {
                echo "Sorry, file already exists.";
                $uploadOk = 0;
            }

            // Check file size
            if ($file["size"] > 5000000) {
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // If everything is ok, try to upload file
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
            } else {
                if (move_uploaded_file($file["tmp_name"], $target_file)) {
                    echo "The file " . htmlspecialchars(basename($file["name"])) . " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }

        public function fetch_all_games() {
            $stmt = $this->conn->prepare("SELECT * FROM games");
            $stmt->execute();
        
            $games = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $game = new Game();
                $game->setID($row['id']);
                $game->set_title($row['title']);
                $game->set_developer($row['developer']);
                $game->set_description($row['description']);
                $game->set_genre($row['genre']);
                $game->set_platform($row['platform']);
                $game->set_releaseyear($row['releaseyear']);
                $game->set_rating($row['rating']);
                $game->set_image($row['image']);
        
                $games[] = $game;
            }
            
            if (empty($games)) {
                echo "<div>No games found in the database.</div>";
            }
        
            return $games;
        }

        public function fetch_game_by_title($id) {
            $stmt = $this->conn->prepare("SELECT * FROM games WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $gameData = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($gameData) {
                $game = new Game();
                $game->setID($gameData['id']);
                $game->set_title($gameData['title']);
                $game->set_description($gameData['description']);
                $game->set_developer($gameData['developer']);
                $game->set_genre($gameData['genre']);
                $game->set_platform($gameData['platform']);
                $game->set_releaseyear($gameData['releaseyear']);
                $game->set_rating($gameData['rating']);
                $game->set_image($gameData['image']);
                return $game;
            }
        }

        public function fetch_first_game_id() {
            $stmt = $this->conn->prepare("SELECT id FROM games ORDER BY id ASC LIMIT 1");
            $stmt->execute();
            $firstGame = $stmt->fetch(PDO::FETCH_ASSOC);
            return $firstGame ? $firstGame['id'] : null;
        }

        public function addToUserGames($userId, $gameId) {
            // Check if the game exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM games WHERE id = :id");
            $stmt->bindParam(':id', $gameId);
            $stmt->execute();
            $gameExists = $stmt->fetchColumn();

            if ($gameExists) {
                // Check if the game is already in the user's wishlist
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user_games WHERE user_id = :user_id AND game_id = :game_id");
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':game_id', $gameId);
                $stmt->execute();
                $alreadyInWishlist = $stmt->fetchColumn();

                if ($alreadyInWishlist) {
                    throw new Exception("Game is already in your wishlist.");
                }

                // Proceed with inserting into user_games
                $stmt = $this->conn->prepare("INSERT INTO user_games (user_id, game_id) VALUES (:user_id, :game_id)");
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':game_id', $gameId);
                $stmt->execute();
            } else {
                throw new Exception("Game ID does not exist.");
            }
        }

        public function fetchUserGames($userId) {
            $stmt = $this->conn->prepare("SELECT games.* FROM games 
                                        JOIN user_games ON games.id = user_games.game_id 
                                        WHERE user_games.user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function removeFromUserGames($userId, $gameId) {
            $stmt = $this->conn->prepare("DELETE FROM user_games WHERE user_id = :user_id AND game_id = :game_id");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return "Game removed from user's list successfully.";
            } else {
                throw new Exception("Error removing game from user's list.");
            }
        }

        public function isGameInUserGames($userId, $gameId) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user_games WHERE user_id = :user_id AND game_id = :game_id");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        }
    }
?>