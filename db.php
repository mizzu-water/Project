<?php
$host = 'localhost';
$db   = 'adora_db';
$user = 'root';  // change if needed
$pass = '';      // change if needed

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Ensure proper Unicode handling
$conn->set_charset('utf8mb4');
?>
