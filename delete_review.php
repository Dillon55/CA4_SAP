<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "You must be an admin to delete reviews.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
    $review_id = $_POST['review_id']; // No sanitization

    // Admins can delete any review (no backend ownership checks)
    $sql = "DELETE FROM reviews WHERE id = $review_id";
    $db->exec($sql); // Vulnerable to SQL injection

    echo "Review deleted successfully!<br>";
    echo "<a href='menu.php'>Return to Menu</a>";
} else {
    echo "Invalid request.";
}
?>
