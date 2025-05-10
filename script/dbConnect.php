<?php
// Database connection
$servername = "localhost";
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "dc_blog"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//If you want to use this connection in other files, you need to return the connection object.
return $conn;
?>
