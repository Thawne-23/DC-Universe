<?php
session_start();

// Create connection
$conn = include '../script/dbConnect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the hero ID (dc_info_id) from the URL
$dc_info_id = isset($_GET['heroId']) ? $_GET['heroId'] : null;

// Fetch character data from the database using the dc_info_id
if ($dc_info_id) {
    $sql = "SELECT * FROM tbl_dc_info WHERE dc_info_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dc_info_id);  // Bind the heroId as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $character = $result->fetch_assoc();
    } else {
        $character = null;
    }
} else {
    $character = null;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DC Character</title>
    <base href="/Dc_Characters/">
    <link rel="icon" type="image/png" href="images/dc_logo.png">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/character.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/comment.css">
    <style>
        .likes-container {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            padding: 10px;
            justify-content: center;
        }

        .like-icon {
            font-size: 2em; /* Adjust size as needed */
            color: #333; /* Default color for general .like-icon */
        }

        /* Added specific rule for character like icon default color */
        .character-like-icon {
            color: #333;
        }


        .like-icon.liked {
             color: #e25364; /* Color when liked */
        }

        .like-count {
            font-size: 0.9em;
            color: #555;
        }
        .reply {
            width: 100%;
        }
        /* Style for profile picture */
        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        .comment-header, .reply-header {
            display: flex;
            align-items: center;
        }
        .likeChar {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center character image and like button */
        }

    </style>
</head>
<body>
    <div class="container">
        <?php include 'navbar.php'; ?>

        <div class="character-container">
            <?php if ($character): ?>
                <div class="likeChar">
                    <img id="image-url" src="<?php echo htmlspecialchars($character['image_url'], ENT_QUOTES); ?>" alt="Character Image"/>
                    <div class="likes-container character-likes-container" onclick="likeCharacter(<?php echo $dc_info_id; ?>)">
                        <span class="like-icon character-like-icon">&#x2764</span>
                        <span class="like-count character-like-count">0</span>
                    </div>
                </div>
                <div class="character-content">
                    <h1 id="image-name"><?php echo htmlspecialchars($character['title'], ENT_QUOTES); ?></h1>
                    <p id="image-description"><?php echo htmlspecialchars($character['description'], ENT_QUOTES); ?></p>
                </div>
            <?php else: ?>
                <p>Character not found.</p>
            <?php endif; ?>
        </div>

        <div class="character-facts">
            <h2>Character Facts</h2>
            <table>
                <tr>
                    <td class="fact-label">Powers:</td>
                    <td id="powers"><?php echo isset($character['powers']) ? htmlspecialchars($character['powers'], ENT_QUOTES) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td class="fact-label">Base of Operations:</td>
                    <td id="operations"><?php echo isset($character['base_of_operations']) ? htmlspecialchars($character['base_of_operations'], ENT_QUOTES) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td class="fact-label">Occupation:</td>
                    <td id="occupation"><?php echo isset($character['occupation']) ? htmlspecialchars($character['occupation'], ENT_QUOTES) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td class="fact-label">Real Name:</td>
                    <td id="real_name"><?php echo isset($character['real_name']) ? htmlspecialchars($character['real_name'], ENT_QUOTES) : 'N/A'; ?></td>
                </tr>
            </table>
        </div>

        <div class="comment-section">
            <h2>Leave a Comment</h2>
            <form id="commentForm">
                <textarea id="commentText" placeholder="Write your comment..." required></textarea>
                <button type="submit">Submit</button>
            </form>

            <div id="commentsList">
                </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <div id="authModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <h2 id="modalTitle">Log In</h2>
            <div id="modalContent"></div>
        </div>
    </div>

    <script src="script/script.js"></script>

    <script>
// Extract parameters from PHP
const user_id = "<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>";
const dc_info_id = "<?php echo isset($_GET['heroId']) ? $_GET['heroId'] : ''; ?>";
const username = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?>";

// Load character likes and comments on page load
document.addEventListener("DOMContentLoaded", preloadData);

function preloadData() {
    fetch(`script/preload_display.php?dc_info_id=${dc_info_id}`)
        .then(response => response.json())
        .then(data => {
            // Preload character likes
            const charLikeCountSpan = document.querySelector('.character-like-count');
            const charLikeIconSpan = document.querySelector('.character-like-icon');

            if (charLikeCountSpan && charLikeIconSpan) {
                charLikeCountSpan.textContent = data.character_likes.count;
                if (data.character_likes.has_liked) {
                    charLikeIconSpan.classList.add('liked');
                } else {
                    charLikeIconSpan.classList.remove('liked');
                }
            }

            // Preload and display comments
            const comments = data.comments;
            const commentContainer = document.getElementById('commentsList');
            commentContainer.innerHTML = ''; // Clear previous content

            comments.forEach(comment => {
                const newComment = document.createElement('div');
                newComment.classList.add('comment');
                newComment.setAttribute('data-id', comment.comment_id);

                // Profile picture for comment (Base64)
                const commentProfilePic = comment.profile_picture
                    ? `<img src="${comment.profile_picture}" alt="${comment.username}'s profile picture" class="profile-pic" />`
                    : `<img src="images/icon.png" alt="Default profile picture" alt="Default profile picture" class="profile-pic" />`;

                const likeIcon = comment.has_liked
                    ? `<span class="like-icon liked" onclick="likeComment(${comment.comment_id})">&#x2764;</span>`
                    : `<span class="like-icon" onclick="likeComment(${comment.comment_id})">&#x2764;</span>`;

                newComment.innerHTML = `
                    <div class="comment-header">
                        ${commentProfilePic}
                        <div class="author">${comment.username}</div>
                    </div>
                    <p>${comment.content}</p>
                    <div class="timestamp">${new Date(comment.timestamp).toLocaleString()}</div>
                    <div class="likes-container">
                        ${likeIcon}
                        <span class="like-count">${comment.like_count}</span>
                    </div>
                    <span class="reply" onclick="showReplyForm(${comment.comment_id})">Reply</span>
                `;

                // Add replies for the comment
                const replyContainer = document.createElement('div');
                comment.replies.forEach(reply => {
                    const replyDiv = document.createElement('div');
                    replyDiv.classList.add('reply');

                    // Profile picture for reply (Base64)
                    const replyProfilePic = reply.profile_picture
                        ? `<img src="${reply.profile_picture}" alt="${reply.username}'s profile picture" class="profile-pic" />`
                        : `<img src="images/icon.png" alt="Default profile picture" class="profile-pic" />`;

                    replyDiv.innerHTML = `
                        <div class="reply-header">
                            ${replyProfilePic}
                            <div class="author">${reply.username} (Reply)</div>
                        </div>
                        <p>${reply.content}</p>
                        <div class="timestamp">${new Date(reply.timestamp).toLocaleString()}</div>
                    `;
                    replyContainer.appendChild(replyDiv);
                });

                newComment.appendChild(replyContainer);
                commentContainer.appendChild(newComment);
            });
        })
        .catch(error => console.error('Error loading data:', error));
}

// Submit new comment
document.getElementById('commentForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const commentText = document.getElementById('commentText').value.trim();
    if (!commentText) return;

    const formData = new FormData();
    formData.append('user_id', user_id);
    formData.append('dc_info_id', dc_info_id);
    formData.append('commentText', commentText);

    fetch('script/comment_submit.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('Comment response:', data);
        if (data.includes("successfully")) {
            document.getElementById('commentText').value = '';
            preloadData(); // Reload comments to show the new one
        }
    })
    .catch(error => console.error('Error:', error));
});

// Like a comment
function likeComment(comment_id) {
    const formData = new FormData();
    formData.append('user_id', user_id);
    formData.append('comment_id', comment_id);

    fetch('script/like_comment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const likeContainer = document.querySelector(`[data-id="${comment_id}"] .likes-container`);
        if (likeContainer) {
            const likeIcon = likeContainer.querySelector('.like-icon');
            const likeCountSpan = likeContainer.querySelector('.like-count');

            if (data.like_count !== undefined) {
                likeCountSpan.textContent = data.like_count;
            }
            if (data.status === 'liked') {
                likeIcon.classList.add('liked');
            } else if (data.status === 'unliked') {
                likeIcon.classList.remove('liked');
            }
        }
    })
    .catch(error => console.error('Error liking comment:', error));
}

// Like a character
function likeCharacter(dc_info_id) {
    const formData = new FormData();
    formData.append('user_id', user_id);
    formData.append('dc_info_id', dc_info_id);

    fetch('script/like_char.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('likeCharacter response data:', data); // Log the response data

        const charLikeCountSpan = document.querySelector('.character-like-count');
        const charLikeIconSpan = document.querySelector('.character-like-icon');

        console.log('Icon element:', charLikeIconSpan); // Log the icon element

        if (charLikeCountSpan && charLikeIconSpan) {
            if (data.like_count !== undefined) {
                charLikeCountSpan.textContent = data.like_count;
            }
            console.log('Current classes before change:', charLikeIconSpan.classList); // Log classes before change

            if (data.status === 'liked') {
                charLikeIconSpan.classList.add('liked');
                console.log('Added "liked" class');
            } else if (data.status === 'unliked') {
                charLikeIconSpan.classList.remove('liked');
                console.log('Removed "liked" class'); // Confirm this line is reached
            }
             console.log('Current classes after change:', charLikeIconSpan.classList); // Log classes after change
        }
    })
    .catch(error => console.error('Error liking character:', error));
}


// Show reply form under a comment
function showReplyForm(comment_id) {
    const commentDiv = document.querySelector(`[data-id="${comment_id}"]`);
    if (!commentDiv.querySelector('.reply-form')) {
        const replyForm = document.createElement('div');
        replyForm.classList.add('reply-form');
        replyForm.innerHTML = `
            <textarea placeholder="Write your reply..." required></textarea>
            <button class="submit-reply" onclick="submitReply(${comment_id})">Submit Reply</button>
        `;
        commentDiv.appendChild(replyForm);
    }
}

// Submit reply to a comment
function submitReply(comment_id) {
    const commentDiv = document.querySelector(`[data-id="${comment_id}"]`);
    const replyText = commentDiv.querySelector('.reply-form textarea').value.trim();
    if (!replyText) return;

    const formData = new FormData();
    formData.append('user_id', user_id);
    formData.append('comment_id', comment_id);
    formData.append('replyText', replyText);

    fetch('script/reply_comment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('Reply response:', data);
        if (data.includes('Reply successfully')) {
            preloadData(); // Reload comments to show reply
        }
    })
    .catch(error => console.error('Error submitting reply:', error));
}
</script>

</body>
</html>