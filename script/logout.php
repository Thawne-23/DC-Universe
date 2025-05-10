<?php
session_start();

// Check if the user is logged in before attempting to log out
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
}
exit;
?>
