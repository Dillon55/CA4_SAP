<?php
session_start();
require 'config.php';

function secureSession() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1); 
    ini_set('session.use_strict_mode', 1);
    session_regenerate_id(true);
}

// Restrict access to admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to view this page.");
}

// Path to the log file
$logFile = __DIR__ . '/app.log';

// Check if the log file exists
if (!file_exists($logFile)) {
    die("Log file not found.");
}

// Read the log file contents
$logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Logs</title>
    <link rel="stylesheet" href="public/css/styles.css"> <!-- Optional styling -->
</head>
<body>
    <h1>Application Logs</h1>
    <a href="menu.php">Return to Menu</a>
    <hr>

    <?php if (!empty($logs)): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <?php 
                    
                    $logParts = explode('] ', $log, 2); 
                    $timestamp = trim($logParts[0], '[]');
                    $message = $logParts[1] ?? 'No message'; 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($timestamp); ?></td>
                        <td><?php echo htmlspecialchars($message); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No logs available.</p>
    <?php endif; ?>
</body>
</html>
