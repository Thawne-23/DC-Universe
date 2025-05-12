<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dc_blog";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$newPassword = $_POST['newPassword'] ?? null;
$profileImageBase64 = $_POST['profileImageBase64'] ?? null;

// Start building the query
$sql = "UPDATE tbl_users SET username = ?, email = ?";
$params = [$username, $email];
$types = "ss";

// Append password if present
if (!empty($newPassword)) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql .= ", user_password = ?";
    $types .= "s";
    $params[] = $hashedPassword;
}

// Append profile image if present
if (!empty($profileImageBase64)) {
    if (!preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $profileImageBase64)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Base64 image format']);
        exit;
    }
    $sql .= ", profile_picture = ?";
    $types .= "s";
    $params[] = $profileImageBase64;
}

// Finalize with WHERE clause
$sql .= " WHERE user_id = ?";
$types .= "i";
$params[] = $userId;

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

// Execute
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
    $_SESSION['username'] = $username;
    $_SESSION['profile_picture'] = $profileImageBase64;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
}

$stmt->close();
$conn->close();
?>
