<?php
// Start the session
session_start();

// Create connection
$conn = include 'dbConnect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data safely
$emailOrUsername = $_POST['email'];
$password = $_POST['password'];

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM tbl_users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists and validate password
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['user_password'])) {
        if($user['user_status'] == 'banned'){
            echo json_encode(["status" => "error", "message" => "You are banned."]);
            exit();
        }
        // Store user info in session
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email']; 
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_type'] = $user['user_type']; 
        echo json_encode([
            "status" => "success",
            "message" => "Login successful! Welcome, " . $user['username'],
            "username" => $user['username'],
            "userId" => $user['user_id'],
            "userType" => $user['user_type']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid password."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No user found with this email or username."]);
}

$stmt->close();
$conn->close();
?>
