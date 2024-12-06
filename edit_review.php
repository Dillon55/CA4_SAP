<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username'])) {
    echo "You must be logged in to edit a review.";
    exit;
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['game_name'], $_POST['new_review'], $_POST['new_recommend'])) {
    $review_id = $_POST['review_id'];// No sanitization 
    $game_name = $_POST['game_name']; 
    $new_review = $_POST['new_review']; // 
    $new_recommend = $_POST['new_recommend']; 


    
    
    $sql = "UPDATE reviews SET game_name = '$game_name', review = '$new_review', recommend = '$new_recommend' WHERE id = $review_id AND customer = '$username'";
    
    $db->exec($sql); // Vulnerable to SQL injection

    echo "Review updated successfully!<br>";
    echo "<a href='menu.php'>Return to Menu</a>";
    echo "SQL Query: " . $sql . "<br>";  // This will print the query to the page
    exit;
}


if (isset($_GET['review_id'])) {
    $review_id = $_GET['review_id'];

   
    $result = $db->query("SELECT * FROM reviews WHERE id = $review_id AND customer = '$username'");
    $row = $result->fetchArray();

    if ($row) {
        echo "<h1>Edit Review</h1>";
        echo "<form method='POST'>
                <input type='hidden' name='review_id' value='" . $row['id'] . "'>
                <label>Game Name:</label><br>
                <input type='text' name='game_name' value='" . $row['game_name'] . "'><br>
                <label>Review:</label><br>
                <textarea name='new_review'>" . $row['review'] . "</textarea><br>
                <label>Would you recommend?:</label><br>
                <label><input type='radio' name='new_recommend' value='Yes' " . 
                    ($row['recommend'] === 'Yes' ? "checked" : "") . "> Yes</label>
                <label><input type='radio' name='new_recommend' value='No' " . 
                    ($row['recommend'] === 'No' ? "checked" : "") . "> No</label><br>
                <input type='submit' value='Update Review'>
              </form>";
    } else {
        echo "You are not authorized to edit this review.";
    }
} else {
    echo "Invalid request.";
}
?>
