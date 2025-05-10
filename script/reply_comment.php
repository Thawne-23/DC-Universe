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
$comment_id = $_POST['comment_id']; // The ID of the comment being replied to
$content = $_POST['replyText']; // The reply content

// Insert the reply into the tbl_replies table
$sql = "INSERT INTO tbl_replies (comment_id, user_id, content) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $comment_id, $user_id, $content);

if ($stmt->execute()) {
    echo "Reply successfully submitted!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
