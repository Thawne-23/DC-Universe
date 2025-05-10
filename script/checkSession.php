<?php
// Start the session
session_start();

if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    // User is logged in, return their username and userId
    echo json_encode([
        "loggedIn" => true,
        "username" => $_SESSION['username'],
        "userId" => $_SESSION['user_id']  
    ]);
} else {
    // User is not logged in
    echo json_encode([
        "loggedIn" => false
    ]);
}
?>
