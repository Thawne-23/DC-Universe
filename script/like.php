<?php
// Start the session
session_start();

// Create connection
$conn = include 'dbConnect.php'; // Assuming dbConnect.php is in the same directory

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate user session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    $conn->close();
    exit();
}

// Validate required POST parameters
if (!isset($_POST['item_id']) || !isset($_POST['item_type']) || !isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request. Item ID, Item Type, or Action missing.']);
    $conn->close();
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = $_POST['item_id'];
$item_type = $_POST['item_type'];
$action = $_POST['action'];

$id_field = null;
$table_name = 'tbl_likes'; // Assuming all likes are stored in this table

// Determine the field name based on the item type
switch ($item_type) {
    case 'comment':
        $id_field = 'comment_id';
        break;
    case 'character':
        $id_field = 'dc_info_id';
        break;
    // Add more cases for other likeable item types if needed
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid item type.']);
        $conn->close();
        exit();
}

if ($action === 'like') {
    // Check if the user has already liked this item
    $sql_check = "SELECT like_id FROM $table_name WHERE user_id = ? AND $id_field = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $user_id, $item_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // If not liked, insert the like
        $sql_insert = "INSERT INTO $table_name (user_id, $id_field) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $user_id, $item_id);
        if ($stmt_insert->execute()) {
            // Get the updated like count
            $sql_count = "SELECT COUNT(*) AS like_count FROM $table_name WHERE $id_field = ?";
            $stmt_count = $conn->prepare($sql_count);
            $stmt_count->bind_param("i", $item_id);
            $stmt_count->execute();
            $result_count = $stmt_count->get_result();
            $row_count = $result_count->fetch_assoc();
            echo json_encode(['status' => 'liked', 'like_count' => $row_count['like_count'], 'item_id' => $item_id, 'item_type' => $item_type, 'message' => 'Liked successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error liking: ' . $stmt_insert->error]);
        }
        $stmt_insert->close();
        $stmt_count->close();
    } else {
        // If already liked, do nothing or return a message indicating it's already liked
        echo json_encode(['status' => 'info', 'message' => 'Already liked.']);
    }
    $stmt_check->close();

} elseif ($action === 'unlike') {
    // Remove the like
    $sql_delete = "DELETE FROM $table_name WHERE user_id = ? AND $id_field = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $user_id, $item_id);
    if ($stmt_delete->execute()) {
        // Get the updated like count
        $sql_count = "SELECT COUNT(*) AS like_count FROM $table_name WHERE $id_field = ?";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->bind_param("i", $item_id);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();
        echo json_encode(['status' => 'unliked', 'like_count' => $row_count['like_count'], 'item_id' => $item_id, 'item_type' => $item_type, 'message' => 'Unliked successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error unliking: ' . $stmt_delete->error]);
    }
    $stmt_delete->close();
    $stmt_count->close();

} elseif ($action === 'count') {
    // Get the like count for a specific item
    $sql_count = "SELECT COUNT(*) AS like_count FROM $table_name WHERE $id_field = ?";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("i", $item_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_assoc();

    if ($row_count) {
        echo json_encode(['status' => 'success', 'like_count' => $row_count['like_count'], 'item_id' => $item_id, 'item_type' => $item_type]);
    } else {
        echo json_encode(['status' => 'success', 'like_count' => 0, 'item_id' => $item_id, 'item_type' => $item_type]);
    }
    $stmt_count->close();

} elseif ($action === 'check') {
    // Check if the current user has liked a specific item
    $sql_check = "SELECT like_id FROM $table_name WHERE user_id = ? AND $id_field = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $user_id, $item_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo json_encode(['status' => 'liked', 'item_id' => $item_id, 'item_type' => $item_type]);
    } else {
        echo json_encode(['status' => 'unliked', 'item_id' => $item_id, 'item_type' => $item_type]);
    }
    $stmt_check->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}

$conn->close();
?>