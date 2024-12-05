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

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "You must be an admin to delete reviews.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['csrf_token'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        logAction("CSRF attempt detected for admin '{$_SESSION['username']}'.");
        echo "Your session has expired or the request is invalid. Please try again.<br><a href='menu.php'>Return to Menu</a>";
        exit;
    }

    $review_id = intval($_POST['review_id']); 

    try {
        $stmt = $db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindValue(':id', $review_id, SQLITE3_INTEGER);
        $result = $stmt->execute();

        if ($result && $db->changes() > 0) {
            logAction("Admin '{$_SESSION['username']}' deleted review ID $review_id.");
            echo "Review deleted successfully!<br><a href='menu.php'>Return to Menu</a>";
        } else {
            logAction("Failed delete attempt for review ID $review_id by admin '{$_SESSION['username']}'.");
            echo "No such review exists or deletion failed.<br><a href='menu.php'>Return to Menu</a>";
        }
    } catch (Exception $e) {
        logAction("Database error during review deletion by admin '{$_SESSION['username']}': " . $e->getMessage());
        echo "An error occurred while processing your request. Please try again later.<br><a href='menu.php'>Return to Menu</a>";
    }
}
?>
