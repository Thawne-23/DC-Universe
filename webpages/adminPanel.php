<?php
session_start();

// Create connection
$conn = include '../script/dbConnect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch all characters
function getAllCharacters($conn) {
    $sql = "SELECT * FROM tbl_dc_info";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to fetch all comments with their replies and users
function getAllCommentsWithReplies($conn) {
    $sql = "SELECT
                c.comment_id,
                c.content AS comment_content,
                c.created_at AS comment_created_at,
                u_c.username AS comment_username,
                d.title AS character_title,
                r.reply_id,
                r.content AS reply_content,
                r.created_at AS reply_created_at,
                u_r.username AS reply_username
            FROM tbl_comments c
            JOIN tbl_users u_c ON c.user_id = u_c.user_id
            JOIN tbl_dc_info d ON c.dc_info_id = d.dc_info_id
            LEFT JOIN tbl_replies r ON c.comment_id = r.comment_id
            LEFT JOIN tbl_users u_r ON r.user_id = u_r.user_id
            ORDER BY c.created_at DESC, r.created_at ASC";
    $result = $conn->query($sql);
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comment_id = $row['comment_id'];
        if (!isset($comments[$comment_id])) {
            $comments[$comment_id] = [
                'comment_id' => $row['comment_id'],
                'content' => $row['comment_content'],
                'created_at' => $row['comment_created_at'],
                'username' => $row['comment_username'],
                'character_title' => $row['character_title'],
                'replies' => []
            ];
        }
        if ($row['reply_id']) {
            $comments[$comment_id]['replies'][] = [
                'reply_id' => $row['reply_id'],
                'content' => $row['reply_content'],
                'created_at' => $row['reply_created_at'],
                'username' => $row['reply_username']
            ];
        }
    }
    return $comments;
}

// Function to fetch all users
function getAllUsers($conn) {
    $sql = "SELECT user_id, username, email, user_status, user_type, created_at FROM tbl_users WHERE user_type = 'regular'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['char_name'])) {
    $title = mysqli_real_escape_string($conn, $_POST['char_name']);
    $full_name = $title;
    $description = mysqli_real_escape_string($conn, $_POST['char_desc']);
    $powers = mysqli_real_escape_string($conn, $_POST['char_powers']);
    $base_of_operations = mysqli_real_escape_string($conn, $_POST['char_base']);
    $occupation = mysqli_real_escape_string($conn, $_POST['char_occupation']);
    $real_name = mysqli_real_escape_string($conn, $_POST['char_realname']);

    $imageTmpPath = $_FILES['char_picture']['tmp_name'];
    $imageData = file_get_contents($imageTmpPath);
    $base64Image = base64_encode($imageData);
    $imageType = mime_content_type($imageTmpPath);
    $base64ImageWithMime = mysqli_real_escape_string($conn, "data:$imageType;base64,$base64Image");

    $sql = "INSERT INTO tbl_dc_info (title, full_name, description, powers, base_of_operations, occupation, real_name, image_url)
            VALUES ('$title', '$full_name', '$description', '$powers', '$base_of_operations', '$occupation', '$real_name', '$base64ImageWithMime')";

    if ($conn->query($sql) === TRUE) {
        echo '<div class="alert alert-success">Character added successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error adding character: ' . $conn->error . '</div>';
    }
}

// Handle deleting a character
if (isset($_GET['delete_character'])) {
    $delete_id = $_GET['delete_character'];
    $sql = "DELETE FROM tbl_dc_info WHERE dc_info_id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        echo '<div class="alert alert-success">Character deleted successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error deleting character: ' . $conn->error . '</div>';
    }
}

// Handle deleting a comment
if (isset($_GET['delete_comment'])) {
    $delete_id = $_GET['delete_comment'];
    // First, delete all replies associated with the comment
    $sql_replies = "DELETE FROM tbl_replies WHERE comment_id = $delete_id";
    if ($conn->query($sql_replies) === TRUE) {
        // Then, delete the comment itself
        $sql_comment = "DELETE FROM tbl_comments WHERE comment_id = $delete_id";
        if ($conn->query($sql_comment) === TRUE) {
            echo '<div class="alert alert-success">Comment and its replies deleted successfully.</div>';
        } else {
            echo '<div class="alert alert-danger">Error deleting comment: ' . $conn->error . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Error deleting replies: ' . $conn->error . '</div>';
    }
}

// Handle deleting a reply
if (isset($_GET['delete_reply'])) {
    $delete_id = $_GET['delete_reply'];
    $sql = "DELETE FROM tbl_replies WHERE reply_id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        echo '<div class="alert alert-success">Reply deleted successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error deleting reply: ' . $conn->error . '</div>';
    }
}

// Handle banning a user
if (isset($_GET['ban_user'])) {
    $ban_id = $_GET['ban_user'];
    $sql = "UPDATE tbl_users SET user_status = 'banned' WHERE user_id = $ban_id";
    if ($conn->query($sql) === TRUE) {
        echo '<div class="alert alert-success">User banned successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error banning user: ' . $conn->error . '</div>';
    }
}

// Handle unbanning a user (optional, for a more complete panel)
if (isset($_GET['unban_user'])) {
    $unban_id = $_GET['unban_user'];
    $sql = "UPDATE tbl_users SET user_status = 'active' WHERE user_id = $unban_id";
    if ($conn->query($sql) === TRUE) {
        echo '<div class="alert alert-success">User unbanned successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error unbanning user: ' . $conn->error . '</div>';
    }
}

// Handle updating a character (NEW)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_char_id'])) {
    $id = $_POST['update_char_id'];
    $title = mysqli_real_escape_string($conn, $_POST['edit_char_name']);
    $full_name = $title; // Same as title
    $description = mysqli_real_escape_string($conn, $_POST['edit_char_desc']);
    $powers = mysqli_real_escape_string($conn, $_POST['edit_char_powers']);
    $base_of_operations = mysqli_real_escape_string($conn, $_POST['edit_char_base']);
    $occupation = mysqli_real_escape_string($conn, $_POST['edit_char_occupation']);
    $real_name = mysqli_real_escape_string($conn, $_POST['edit_char_realname']);

    // Default to old Base64 image (if no new image is uploaded)
    $image_url = $_POST['edit_char_old_picture'];

    if (!empty($_FILES['edit_char_picture']['tmp_name'])) {
        // Convert new uploaded file to base64
        $imageTmpPath = $_FILES['edit_char_picture']['tmp_name'];
        $imageData = file_get_contents($imageTmpPath);
        $base64Image = base64_encode($imageData);
        $imageType = mime_content_type($imageTmpPath); // e.g., image/png
        $image_url = "data:$imageType;base64,$base64Image";
    }

    // Update the database
    $sql = "UPDATE tbl_dc_info SET
                title = '$title',
                full_name = '$full_name',
                description = '$description',
                powers = '$powers',
                base_of_operations = '$base_of_operations',
                occupation = '$occupation',
                real_name = '$real_name',
                image_url = '$image_url'
            WHERE dc_info_id = $id";

    if ($conn->query($sql) === TRUE) {
        echo '<div class="alert alert-success">Character updated successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error updating character: ' . $conn->error . '</div>';
    }
}



$characters = getAllCharacters($conn);
$commentsWithReplies = getAllCommentsWithReplies($conn);
$users = getAllUsers($conn);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { padding: 40px; }
        .tab-content { margin-top: 20px; }
        .btn-xs {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
            line-height: 1;
            border-radius: 0.2rem;
        }
        .gap-2 > * {
            margin-left: 0.5rem;
        }

    </style>
</head>
<body>
    <div class="container">
         <div style="display: flex; justify-content: space-between; align-items: center;">
            <a href="home.php" class="btn btn-outline-secondary mb-3">&larr; Back to Home</a>
            <div>
                <a href="editProfile.php?id=<?php echo $_SESSION['user_id']; ?>&isAdmin=true" class="btn btn-primary">Edit Profile</a>
                <a id="logout-btn" class="btn btn-danger">Log Out</a>
            </div>
        </div>
        <h2 class="mb-4">Admin Dashboard</h2>

        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="characters-tab" data-toggle="tab" href="#characters" role="tab">Characters</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="replies-tab" data-toggle="tab" href="#replies" role="tab">Comments/Replies</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="users-tab" data-toggle="tab" href="#users" role="tab">Users</a>
            </li>
        </ul>

        <div class="tab-content" id="adminTabsContent">
            <div class="tab-pane fade show active" id="characters" role="tabpanel">
                <h4 class="mt-3">Add New Character</h4>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Character Name</label>
                        <input type="text" name="char_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="char_desc" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Picture</label>
                        <input type="file" name="char_picture" class="form-control-file" required>
                    </div>
                    <div class="form-group">
                        <label>Powers</label>
                        <textarea name="char_powers" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Base of Operations</label>
                        <input type="text" name="char_base" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Occupation</label>
                        <input type="text" name="char_occupation" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Real Name</label>
                        <input type="text" name="char_realname" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Character</button>
                </form>

                <hr>
                <h5>Existing Characters</h5>
                <?php if (empty($characters)): ?>
                    <p>No characters found.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($characters as $character): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($character['title']); ?>
                                <div>
                                    <button class="btn btn-sm btn-warning edit-character-btn" data-toggle="modal" data-target="#editCharacterModal"
                                            data-id="<?php echo $character['dc_info_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($character['title']); ?>"
                                            data-desc="<?php echo htmlspecialchars($character['description']); ?>"
                                            data-powers="<?php echo htmlspecialchars($character['powers']); ?>"
                                            data-base="<?php echo htmlspecialchars($character['base_of_operations']); ?>"
                                            data-occupation="<?php echo htmlspecialchars($character['occupation']); ?>"
                                            data-realname="<?php echo htmlspecialchars($character['real_name']); ?>"
                                            data-picture="<?php echo htmlspecialchars($character['image_url']); ?>">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirmDeleteModal<?php echo $character['dc_info_id']; ?>">Delete</button>
                                </div>
                            </li>
                            <div class="modal fade" id="confirmDeleteModal<?php echo $character['dc_info_id']; ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Delete</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">Are you sure you want to delete <strong><?php echo htmlspecialchars($character['title']); ?></strong>?</div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <a href="adminPanel.php?delete_character=<?php echo $character['dc_info_id']; ?>" class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="modal fade" id="editCharacterModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Character</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="update_char_id" id="edit_char_id">
                                    <input type="hidden" name="edit_char_old_picture" id="edit_char_old_picture">
                                    <div class="form-group">
                                        <label>Character Name</label>
                                        <input type="text" name="edit_char_name" id="edit_char_name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="edit_char_desc" id="edit_char_desc" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Picture</label>
                                        <input type="file" name="edit_char_picture" class="form-control-file">
                                        <img src="" id="edit_char_image_preview" alt="Current Picture" style="max-width: 200px;">
                                    </div>
                                    <div class="form-group">
                                        <label>Powers</label>
                                        <textarea name="edit_char_powers" id="edit_char_powers" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Base of Operations</label>
                                        <input type="text" name="edit_char_base" id="edit_char_base" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Occupation</label>
                                        <input type="text" name="edit_char_occupation" id="edit_char_occupation" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Real Name</label>
                                        <input type="text" name="edit_char_realname" id="edit_char_realname" class="form-control">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Character</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="replies" role="tabpanel">
                <h4 class="mt-3">Comments & Replies Management</h4>
                <?php if (empty($commentsWithReplies)): ?>
                    <p>No comments found.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($commentsWithReplies as $comment): ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($comment['username']); ?></strong> on 
                                <strong><?php echo htmlspecialchars($comment['character_title']); ?></strong>: 
                                <?php echo htmlspecialchars($comment['content']); ?>
                                
                                <ul class="list-group mt-2 ml-4">
                                    <?php if (!empty($comment['replies'])): ?>
                                        <?php foreach ($comment['replies'] as $reply): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <strong><?php echo htmlspecialchars($reply['username']); ?>:</strong> 
                                                    <?php echo htmlspecialchars($reply['content']); ?>
                                                </span>
                                                <button class="btn btn-xs btn-outline-secondary" 
                                                        data-toggle="modal" 
                                                        data-target="#confirmReplyDeleteModal<?php echo $reply['reply_id']; ?>">
                                                    x
                                                </button>
                                            </li>

                                            <!-- Modal for confirming reply deletion -->
                                            <div class="modal fade" id="confirmReplyDeleteModal<?php echo $reply['reply_id']; ?>" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Reply Deletion</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete this reply by 
                                                            <strong><?php echo htmlspecialchars($reply['username']); ?></strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <a href="adminPanel.php?delete_reply=<?php echo $reply['reply_id']; ?>" class="btn btn-danger">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item">No replies yet.</li>
                                    <?php endif; ?>
                                </ul>

                                <!-- Button to remove comment -->
                                <div class="mt-2 text-right">
                                    <button class="btn btn-sm btn-danger" 
                                            data-toggle="modal" 
                                            data-target="#confirmCommentDeleteModal<?php echo $comment['comment_id']; ?>">
                                        Remove
                                    </button>
                                </div>
                            </li>

                            <!-- Modal for confirming comment deletion -->
                            <div class="modal fade" id="confirmCommentDeleteModal<?php echo $comment['comment_id']; ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Comment Deletion</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to remove this comment by 
                                            <strong><?php echo htmlspecialchars($comment['username']); ?></strong> 
                                            and all its replies?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <a href="adminPanel.php?delete_comment=<?php echo $comment['comment_id']; ?>" class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="users" role="tabpanel">
            <h4 class="mt-3">User Management</h4>

            <?php if (empty($users)): ?>
                <p>No users found.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($users as $user): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong> - <?php echo htmlspecialchars($user['email']); ?>
                            </div>

                            <div class="d-flex align-items-center">
                                <?php if ($user['user_status'] === 'active'): ?>
                                    <button class="btn btn-sm btn-dark" data-toggle="modal" data-target="#confirmBanModal<?php echo $user['user_id']; ?>">Ban</button>
                                <?php elseif ($user['user_status'] === 'banned'): ?>
                                    <a href="adminPanel.php?unban_user=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-success">Unban</a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-dark" data-toggle="modal" data-target="#confirmBanModal<?php echo $user['user_id']; ?>">Ban</button>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Modals should go OUTSIDE the list -->
                <?php foreach ($users as $user): ?>
                    <?php if ($user['user_status'] === 'active'): ?>
                        <div class="modal fade" id="confirmBanModal<?php echo $user['user_id']; ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirm Ban</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to ban <strong><?php echo htmlspecialchars($user['username']); ?></strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <a href="adminPanel.php?ban_user=<?php echo $user['user_id']; ?>" class="btn btn-dark">Ban</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.edit-character-btn').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var desc = $(this).data('desc');
                var powers = $(this).data('powers');
                var base = $(this).data('base');
                var occupation = $(this).data('occupation');
                var realname = $(this).data('realname');
                var picture = $(this).data('picture');

                $('#edit_char_id').val(id);
                $('#edit_char_name').val(name);
                $('#edit_char_desc').val(desc);
                $('#edit_char_powers').val(powers);
                $('#edit_char_base').val(base);
                $('#edit_char_occupation').val(occupation);
                $('#edit_char_realname').val(realname);
                $('#edit_char_image_preview').attr('src', picture);
                $('#edit_char_old_picture').val(picture); // Store old picture URL
            });
        });
        document.getElementById("logout-btn").addEventListener("click", function () {
            fetch('../script/logout.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                alert("Logged out successfully!");
                window.location.href = 'home.php';
                } else {
                alert("Error logging out: " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while logging out.');
            });
        });
    </script>
</body>
</html>