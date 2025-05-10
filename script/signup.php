<?php
// Create connection
$conn = include 'dbConnect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data from POST
$username = $_POST['username'];
$email = $_POST['email'];
$user_password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password for security
$user_status = 'active';  // Default status, could be 'active' or 'inactive'
$user_type = 'regular';   // Default type, could be 'regular' or 'admin'

// SQL query to insert data
$sql = "INSERT INTO tbl_users (username, email, user_password, user_status, user_type) 
        VALUES ('$username', '$email', '$user_password', '$user_status', '$user_type')";

if ($conn->query($sql) === TRUE) {
    echo "Signup successful!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
