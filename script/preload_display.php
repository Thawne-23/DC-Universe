<?php
// Start the session
session_start();

// Create connection
$conn = include 'dbConnect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$dc_info_id = $_GET['dc_info_id']; // The ID of the DC character being viewed
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Get user ID if logged in

// Fetch character like information
$char_like_count = 0;
$char_has_liked = false;
if ($dc_info_id) {
    $char_like_sql = "SELECT COUNT(*) as like_count FROM tbl_likes WHERE dc_info_id = ?";
    $char_like_stmt = $conn->prepare($char_like_sql);
    $char_like_stmt->bind_param("i", $dc_info_id);
    $char_like_stmt->execute();
    $char_like_result = $char_like_stmt->get_result();
    $char_like_count = $char_like_result->fetch_assoc()['like_count'];
    $char_like_stmt->close();

    if ($user_id) {
        $char_user_liked_sql = "SELECT like_id FROM tbl_likes WHERE user_id = ? AND dc_info_id = ?";
        $char_user_liked_stmt = $conn->prepare($char_user_liked_sql);
        $char_user_liked_stmt->bind_param("ii", $user_id, $dc_info_id);
        $char_user_liked_stmt->execute();
        $char_has_liked = $char_user_liked_stmt->get_result()->num_rows > 0;
        $char_user_liked_stmt->close();
    }
}

// Fetch comments with usernames and profile pictures
$sql = "SELECT c.comment_id, c.user_id, c.content, c.created_at, u.username, u.profile_picture
        FROM tbl_comments c
        JOIN tbl_users u ON c.user_id = u.user_id
        WHERE c.dc_info_id = ? ORDER BY c.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dc_info_id);
$stmt->execute();
$comments = $stmt->get_result();

$comments_array = [];

while ($comment = $comments->fetch_assoc()) {
    $comment_id = $comment['comment_id'];

    // Fetch like count for comment
    $like_sql = "SELECT COUNT(*) as like_count FROM tbl_likes WHERE comment_id = ?";
    $like_stmt = $conn->prepare($like_sql);
    $like_stmt->bind_param("i", $comment_id);
    $like_stmt->execute();
    $like_result = $like_stmt->get_result();
    $like_count = $like_result->fetch_assoc()['like_count'];
    $like_stmt->close();

    // Check if current user liked the comment
    $has_liked = false;
    if ($user_id) {
        $user_liked_sql = "SELECT like_id FROM tbl_likes WHERE user_id = ? AND comment_id = ?";
        $user_liked_stmt = $conn->prepare($user_liked_sql);
        $user_liked_stmt->bind_param("ii", $user_id, $comment_id);
        $user_liked_stmt->execute();
        $user_liked_result = $user_liked_stmt->get_result();
        $has_liked = $user_liked_result->num_rows > 0;
        $user_liked_stmt->close();
    }

    // Fetch replies with usernames and profile pictures
    $reply_sql = "SELECT r.user_id, r.content, r.created_at, u.username, u.profile_picture
                  FROM tbl_replies r
                  JOIN tbl_users u ON r.user_id = u.user_id
                  WHERE r.comment_id = ? ORDER BY r.created_at DESC";
    $reply_stmt = $conn->prepare($reply_sql);
    $reply_stmt->bind_param("i", $comment_id);
    $reply_stmt->execute();
    $replies = $reply_stmt->get_result();

    $replies_array = [];
    while ($reply = $replies->fetch_assoc()) {
        $replies_array[] = [
            'user_id' => $reply['user_id'],
            'username' => $reply['username'],
            'profile_picture' => $reply['profile_picture'],
            'content' => $reply['content'],
            'timestamp' => $reply['created_at']
        ];
    }
    $reply_stmt->close();

    $comments_array[] = [
        'comment_id' => $comment_id,
        'user_id' => $comment['user_id'],
        'username' => $comment['username'],
        'profile_picture' => $comment['profile_picture'],
        'content' => $comment['content'],
        'like_count' => $like_count,
        'has_liked' => $has_liked,
        'replies' => $replies_array,
        'timestamp' => $comment['created_at']
    ];
}

echo json_encode([
    'character_likes' => ['count' => $char_like_count, 'has_liked' => $char_has_liked],
    'comments' => $comments_array
]);

$stmt->close();
$conn->close();
?>