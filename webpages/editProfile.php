<?php
session_start();

// Check if user is logged in and URL contains a user ID
if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
  
  if ($_SESSION['user_type'] == 'admin' && !isset($_GET['isAdmin'])  ) {
    header('Location: adminPanel.php');
    exit(); 
  }
    $userIdFromURL = intval($_GET['id']);
    $sessionUserId = intval($_SESSION['user_id']);

    if ($userIdFromURL === $sessionUserId) {
       // Create connection
        $conn = include '../script/dbConnect.php';

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch user data
        $sql = "SELECT username, email, profile_picture FROM tbl_users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userIdFromURL);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $username = $user['username'];
            $email = $user['email'];
            $profilePicture = $user['profile_picture']
                ? (strpos($user['profile_picture'], 'data:image/') === false
                    ? 'data:image/jpeg;base64,' . $user['profile_picture']
                    : $user['profile_picture'])
                : '../images/icon.png';
        } else {
            echo "User not found.";
            exit;
        }

        $stmt->close();
        $conn->close();
    } else {
        // Unauthorized
        echo "You are not allowed to edit this profile.";
        exit;
    }
} else {
    // Missing ID or session
    echo "Invalid access.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .profile-pic {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #ccc;
      margin-bottom: 10px;
    }
    .upload-label {
      cursor: pointer;
      color: #0d6efd;
      font-size: 14px;
    }
    #imageUpload {
      display: none;
    }
  </style>
</head>
<body class="bg-light">

    <div class="container mt-4">
        <div class="card mx-auto" style="max-width: 450px;">
          <div class="card-body">
            
            <!-- Back Button + Title Row -->
            <div class="d-flex align-items-center mb-3">
            <button onclick="history.back()" class="btn btn-outline-secondary btn-sm me-2">←</button>
              <h5 class="mb-0">Edit Profile</h5>
            </div>
      
            <!-- Profile Picture -->
            <div class="text-center">
              <img src="<?php echo $profilePicture; ?>" alt="Profile" class="profile-pic" id="profileImage">
              <input type="file" id="imageUpload" accept="image/*">
              <div>
                <label for="imageUpload" class="upload-label">Change Photo</label>
              </div>
            </div>

      <form id="editProfileForm" class="text-start mt-4">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" placeholder="Enter username" value="<?php echo $username; ?>">
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email (read-only)</label>
          <input type="email" class="form-control" id="email" value="<?php echo $email; ?>" readonly>
        </div>

        <div class="mb-3">
          <button type="button" class="btn btn-secondary w-100" id="togglePasswordBtn">Change Password</button>
        </div>

        <div id="passwordFields" style="display: none;">
          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" class="form-control" id="newPassword" placeholder="New password">
          </div>

          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm password">
          </div>
        </div>

        <div class="d-grid mb-2">
          <button type="submit" class="btn btn-primary" id="saveBtn" style="display: none;">Save Changes</button>
        </div>
      </form>

      <div class="d-grid">
        <button id="logoutBtn" class="btn btn-danger">Log Out</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const imageUpload = document.getElementById("imageUpload");
  const profileImage = document.getElementById("profileImage");
  const togglePasswordBtn = document.getElementById("togglePasswordBtn");
  const passwordFields = document.getElementById("passwordFields");
  const saveBtn = document.getElementById("saveBtn");
  const usernameInput = document.getElementById("username");
  const originalUsername = usernameInput.value;

  let imageChanged = false;
  let passwordShown = false;

  imageUpload.addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
      profileImage.src = URL.createObjectURL(file);
      imageChanged = true;
      toggleSaveButton();
    }
  });

  togglePasswordBtn.addEventListener("click", () => {
    passwordShown = !passwordShown;
    passwordFields.style.display = passwordShown ? "block" : "none";
    toggleSaveButton();
  });

  usernameInput.addEventListener("input", () => {
    toggleSaveButton();
  });

  function toggleSaveButton() {
    const usernameChanged = usernameInput.value.trim() !== originalUsername.trim();
    if (usernameChanged || imageChanged || passwordShown) {
      saveBtn.style.display = "block";
    } else {
      saveBtn.style.display = "none";
    }
  }

  document.getElementById("editProfileForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const newPassword = document.getElementById("newPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (passwordFields.style.display === "block" && newPassword !== confirmPassword) {
      alert("Passwords do not match!");
      return;
    }

    const formData = new FormData();
    formData.append("username", username);
    formData.append("email", email);
    if (passwordFields.style.display === "block" && newPassword.length > 0) {
      formData.append("newPassword", newPassword);
    }

    const profileImageFile = imageUpload.files[0];
    if (profileImageFile) {
      const reader = new FileReader();
      reader.onloadend = function () {
        const base64Image = reader.result;
        formData.append("profileImageBase64", base64Image);
        sendProfileUpdate(formData);
      };
      reader.readAsDataURL(profileImageFile);
    } else {
      sendProfileUpdate(formData);
    }
  });

  function sendProfileUpdate(formData) {
    fetch('../script/updateProfile.php', {
      method: 'POST',
      body: formData,
      credentials: 'include'
    })
    .then(async response => {
      const text = await response.text();
      console.log("Raw response:", text);
      try {
        const data = JSON.parse(text);
        if (data.status === "success") {
          alert("Profile updated successfully!");
          saveBtn.style.display = "none";
          passwordFields.style.display = "none";
        } else {
          alert("Error updating profile: " + data.message);
        }
      } catch (e) {
        console.error('JSON parse error:', e);
        alert("Server returned an invalid response.");
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      alert('An error occurred while updating the profile.');
    });
  }

  document.getElementById("logoutBtn").addEventListener("click", function () {
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
