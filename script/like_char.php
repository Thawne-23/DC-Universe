<?php
// Start the session
session_start();

// Create connection
$conn = include 'dbConnect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validation
if (!isset($_SESSION['user_id']) || !isset($_POST['dc_info_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request. User ID or Character ID missing.']);
    $conn->close();
    exit();
}

$user_id = $_SESSION['user_id'];
$dc_info_id = $_POST['dc_info_id'];

// Check if the user has already liked this character
$sql_check = "SELECT like_id FROM tbl_likes WHERE user_id = ? AND dc_info_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $dc_info_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    // If not liked, insert the like
    $sql_insert = "INSERT INTO tbl_likes (user_id, dc_info_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $user_id, $dc_info_id);
    if ($stmt_insert->execute()) {
        // Get the updated like count
        $sql_count = "SELECT COUNT(*) AS like_count FROM tbl_likes WHERE dc_info_id = ?";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->bind_param("i", $dc_info_id);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();
        echo json_encode(['status' => 'liked', 'like_count' => $row_count['like_count'], 'message' => 'Character liked successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error liking character: ' . $stmt_insert->error]);
    }
    $stmt_insert->close();
    $stmt_count->close();
} else {
    // If already liked, remove the like (unlike)
    $sql_delete = "DELETE FROM tbl_likes WHERE user_id = ? AND dc_info_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $user_id, $dc_info_id);
    if ($stmt_delete->execute()) {
        // Get the updated like count
        $sql_count = "SELECT COUNT(*) AS like_count FROM tbl_likes WHERE dc_info_id = ?";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->bind_param("i", $dc_info_id);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();
        echo json_encode(['status' => 'unliked', 'like_count' => $row_count['like_count'], 'message' => 'Character unliked successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error unliking character: ' . $stmt_delete->error]);
    }
    $stmt_delete->close();
    $stmt_count->close();
}

$stmt_check->close();
$conn->close();
?>