<?php
$host = 'localhost';
$user = 'root'; // Default XAMPP/WAMP username
$pass = '';     // Default XAMPP/WAMP password
$dbname = 'Website01';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
