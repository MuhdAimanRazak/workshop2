<?php
// config/config.php

$host = "localhost";
$user = "root";
$pass = "";        // XAMPP default is empty
$db   = "myhostel";  // Make sure this matches your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8mb4");
?>
