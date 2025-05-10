<?php
// Start the session
session_start();

// Create connection
$conn = include 'dbConnect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id']; // Assuming the user ID is stored in the session
$dc_info_id = $_POST['dc_info_id']; // The ID of the DC character being commented on
$content = $_POST['commentText']; // The comment text

// Insert the comment into the tbl_comments table
$sql = "INSERT INTO tbl_comments (dc_info_id, user_id, content) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $dc_info_id, $user_id, $content);

if ($stmt->execute()) {
    echo "Comment successfully submitted!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
