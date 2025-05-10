const fetchCharacter = async (heroName) => {
    try {
        const response = await fetch(`script/fetchCharacter.php?name=${encodeURIComponent(heroName)}`);
        const result = await response.json();

        if (result.status === "success") {
            const hero = result.data;

            const heroDetails = {
                name: hero.title,
                imageUrl: hero.image_url, // Adjust if your DB uses different column names
                description: hero.description,
                facts: {
                    powers: hero.powers,
                    base_of_operations: hero.base_of_operations,
                    occupation: hero.occupation || "Unknown",
                    real_name: hero.real_name
                },
                heroId: hero.dc_info_id,
            };

            return heroDetails;
        } else {
            console.error("Character not found:", result.message);
            return null;
        }
    } catch (error) {
        console.error("Error fetching character:", error);
        return null;
    }
};



// Handle image click on index.php
document.addEventListener("DOMContentLoaded", () => {
        // Check if the user is logged in when the page loads
        fetch('script/checkSession.php')
        .then(res => res.json())
        .then(data => {
            if (data.loggedIn) {
                // Update the DOM to show the logged-in user
                const regis = document.querySelector(".register");
                if (regis) {
                    regis.innerHTML = `<li><a href="webpages/editProfile.php?id=${data.userId}" id="newLogIn">Logged in as ${data.username}</a></li>`;
                }
            }
        })
        .catch(err => console.error("Session check error:", err));

        document.querySelectorAll('.character-image').forEach(image => {
            image.addEventListener('click', async () => {
                const heroName = image.getAttribute('alt'); // Get character name
                const heroData = await fetchCharacter(heroName);
            
                if (heroData) {
                    // Get userId from checkSession.php
                    const sessionResponse = await fetch('script/checkSession.php');
                    const sessionData = await sessionResponse.json();
            
                    if (sessionData.loggedIn) {
                        const userId = sessionData.userId;
            
                        // Store character data in localStorage if needed
                        localStorage.setItem('selectedCharacter', JSON.stringify(heroData));
            
                        // Redirect to character.php with both IDs as query parameters
                        window.location.href = `webpages/character.php?heroId=${heroData.heroId}&userId=${userId}`;
                    } else {
                        console.log("User not logged in.");
                    }
                } else {
                    console.log("Character not found.");
                }
            });
            
          });

    setTimeout(() => {
        document.querySelector(".splash-screen").classList.add("slide-down");
        document.querySelector(".main-content").classList.add("fade-in");
    }, 3000);
});

// Auth Modal Handling
document.getElementById("logIn").addEventListener("click", function () {
    openAuthModal("login");
});

document.getElementById("signUp").addEventListener("click", function () {
    openAuthModal("signup");
});

document.getElementById("closeModal").addEventListener("click", function () {
    closeAuthModal();
});

// In script/script.js or <script> tags
function handleViewSiteClick() {
    // Your JavaScript code here
    console.log("View Full Site button clicked!");
  
    // To navigate to a new page:
    window.location.href = "webpages/allCharacters.php"; // Replace with the actual URL
}

function openAuthModal(type) {
    const modal = document.getElementById("authModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalContent = document.getElementById("modalContent");
    console.log('here');
    modal.style.display = "block"; // Show modal

    if (type === "login") {
        modalTitle.innerText = "Log In";
        modalContent.innerHTML = `
            <form id="loginForm">
                <input type="text" id="loginEmail" placeholder="Email/Username" required>
                <input type="password" id="loginPassword" placeholder="Password" required>
                <button type="submit">Log In</button>
                <p>Don't have an account? <a href="javascript:void(0);" onclick="openAuthModal('signup')">Sign Up</a></p>
            </form>
        `;
        document.getElementById("loginForm").addEventListener("submit", handleLogin);
    } else {
        modalTitle.innerText = "Sign Up";
        modalContent.innerHTML = `
            <form id="signupForm">
                <input type="text" id="signupName" placeholder="Full Name" required>
                <input type="email" id="signupEmail" placeholder="Email" required>
                <input type="password" id="signupPassword" placeholder="Password" required>
                <input type="password" id="conPassword" placeholder="Confirm Password" required>
                <button type="submit">Sign Up</button>
                <p>Already have an account? <a href="javascript:void(0);" onclick="openAuthModal('login')">Log In</a></p>
            </form>
        `;
        document.getElementById("signupForm").addEventListener("submit", handleSignup);
    }
}

function closeAuthModal() {
    document.getElementById("authModal").style.display = "none";
}

function handleLogin(event) {
    event.preventDefault();
    const email = document.getElementById("loginEmail").value;
    const password = document.getElementById("loginPassword").value;

    console.log("Logging in with:", email, password);

    // Send the data to PHP script for validation
    fetch('script/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
    .then(response => response.json()) // Parse the JSON response
    .then(data => {
        console.log(data);  // Log the response from PHP script
    
        if (data.status === "success") {  // Check if login is successful
            alert(data.message);
            closeAuthModal();
            
            // Get the first element with the class "register"
            var regis = document.querySelector(".register");
    
            // Check if the element exists
            if (regis) {
                // Clear the current content inside the "register" element
                regis.innerHTML = "";
                 
                // Add new content inside the "register" element
                regis.innerHTML = `
                    <li><a href="webpages/editProfile.php?id=${data.userId}" id="newLogIn">Logged in as ${data.username}</a></li>
                `;
            }
    
            // Optionally, redirect to a different page after successful login
            // window.location.href = "home.php"; // Example redirect
        } else {
            alert("Login failed: " + data.message);  // Show error message from PHP if any
        }
    })
    .catch(error => {
        console.error('Error:', error);  // Handle errors in fetching or parsing the response
    });
}


function handleSignup(event) {
    event.preventDefault();
    const name = document.getElementById("signupName").value;
    const email = document.getElementById("signupEmail").value;
    const password = document.getElementById("signupPassword").value;
    const conpassword = document.getElementById("conPassword").value;

    if(password != conpassword){
        alert("Password doesnt match");
        return
    }

    console.log("Signing up with:", name, email, password);

    // Send the data to PHP script using fetch
    fetch('script/signup.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `username=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
    .then(response => response.text())  // Expect the response as text
    .then(data => {
        console.log(data);  // Response from PHP script
        if (data === "Signup successful!") {
            alert("Signup successful!");
            closeAuthModal();
        } else {
            alert("Signup failed: " + data);  // Show error message from PHP if any
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred.");
    });
}


// Menu Toggle
function toggleMenu() {
    const navMenu = document.getElementById("nav-menu");
    
    if (navMenu.style.display === "flex") {
      navMenu.style.display = "none";
    } else {
      navMenu.style.display = "flex";
      navMenu.style.flexDirection = "column";
      navMenu.style.width = "100%";
    }
}