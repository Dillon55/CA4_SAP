<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username'])) {
    echo "You must be logged in to add a review!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game_name = $_POST['game_name'];  // No input validation Vulnerable to XSS
    $review = $_POST['review'];  // 
    $recommend = $_POST['recommend'];  
    
    // SQL Injection risk due to lack of prepared statements
    $sql = "INSERT INTO reviews (game_name, review, recommend, customer) VALUES ('$game_name', '$review', '$recommend', '".$_SESSION['username']."')";
    $db->exec($sql);  // Direct SQL execution with unsanitized user input

    echo "Review added! You can now <a href='menu.php'>menu</a>.";
    echo "SQL Query: " . $sql . "<br>";  // This will print the query to the page
}
?>

<h1>Add a Game Review</h1>
<form method="POST">
    Game Name: <input type="text" name="game_name" required><br>
    What did you like or dislike about the game?: <textarea name="review" required></textarea><br>
    Would you recommend?: 
    <label><input type="radio" name="recommend" value="Yes" required> Yes</label>
    <label><input type="radio" name="recommend" value="No" required> No</label><br>
    <input type="submit" value="Submit Review">
</form>
