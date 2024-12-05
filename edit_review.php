<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_strict_mode', 1);

session_start();
require 'config.php';

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self';");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

function secureSession() {
    session_regenerate_id(true); 
}


secureSession();

if (!isset($_SESSION['username'])) {
    echo "You must be logged in to edit a review.";
    exit;
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['csrf_token'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        logAction("CSRF attempt detected for user '$username'.");
        die("Invalid CSRF token.");
    }

    $review_id = (int) $_POST['review_id'];
    $game_name = htmlspecialchars($_POST['game_name'], ENT_QUOTES, 'UTF-8');
    $new_review = htmlspecialchars($_POST['new_review'], ENT_QUOTES, 'UTF-8');
    $new_recommend = htmlspecialchars($_POST['new_recommend'], ENT_QUOTES, 'UTF-8');

    if (!in_array($new_recommend, ['Yes', 'No'])) {
        echo "Invalid recommendation value.";
        exit;
    }

    $stmt = $db->prepare("UPDATE reviews SET game_name = :game_name, review = :new_review, recommend = :new_recommend WHERE id = :review_id AND customer = :username");
    $stmt->bindValue(':game_name', $game_name, SQLITE3_TEXT);
    $stmt->bindValue(':new_review', $new_review, SQLITE3_TEXT);
    $stmt->bindValue(':new_recommend', $new_recommend, SQLITE3_TEXT);
    $stmt->bindValue(':review_id', $review_id, SQLITE3_INTEGER);
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);

    if ($stmt->execute()) {
        logAction("User '$username' updated review ID $review_id.");
        echo "Review updated successfully!<br><a href='menu.php'>Return to Menu</a>";
    } else {
        echo "Error updating review.";
        logAction("Error updating review for user '$username' with review ID $review_id.");
    }
    exit;
}

if (isset($_GET['review_id'])) {
    $review_id = (int) $_GET['review_id'];
    $stmt = $db->prepare("SELECT * FROM reviews WHERE id = :review_id AND customer = :username");
    $stmt->bindValue(':review_id', $review_id, SQLITE3_INTEGER);
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);

    if ($row) {
        echo "<form method='POST'>
                <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token']) . "'>
                <input type='hidden' name='review_id' value='" . htmlspecialchars($row['id']) . "'>
                
                <label>Game Name:</label><br>
                <input type='text' name='game_name' value='" . htmlspecialchars($row['game_name']) . "' required><br>
                
                <label>What did you like or dislike about the game?:</label><br>
                <textarea name='new_review' required>" . htmlspecialchars($row['review']) . "</textarea><br>
                
                <label>Would you recommend?:</label><br>
                <label><input type='radio' name='new_recommend' value='Yes' " . 
                    ($row['recommend'] === 'Yes' ? "checked" : "") . "> Yes</label>
                <label><input type='radio' name='new_recommend' value='No' " . 
                    ($row['recommend'] === 'No' ? "checked" : "") . "> No</label><br>
                
                <input type='submit' value='Update Review'>
              </form>";
    } else {
        echo "Unauthorized access or review not found.";
    }
} else {
    echo "Invalid request.";
}
?>
