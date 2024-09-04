<?php
// Example database connection configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'local';
//$port = 3307; // Assuming MySQL port 3307 based on your description

// Create a mysqli connection
$mysqli = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
