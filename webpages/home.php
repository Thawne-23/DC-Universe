<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DC Universe</title>
  <base href="/Dc_Characters/">
  <link rel="icon" type="image/png" href="images/dc_logo.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/carousel.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/modal.css">
  <link rel="stylesheet" href="css/home.css">
  <style>
    
    @media (max-width: 768px) {
      .image-gallery {
        justify-content: center; /* Center images in smaller screens */
      }

      .image-item {
        flex: 1 1 45%; /* Almost half-width per row */
        max-width: 200px;
        text-align: center; /* Center text */
      }

      .image-gallery img {
        max-width: 200px; /* Smaller image size */
      }

      /* Further reduce text size for small screens */
      .image-name {
        font-size: 14px;
      }

      .section h2 {
        font-size: 18px;
        text-align: center;
      }

      .another {
        display: flex;
        flex-direction: column; /* Stack elements vertically */
        align-items: center; /* Center items horizontally */
        justify-content: center; /* Center items vertically (if needed) */
        text-align: center; /* Ensures text inside is centered */
      }

      .another p {
        font-size: 12px;
        max-width: 300px;
        text-align: center; /* Center the text itself */
      }

      .carousel-wrapper {
         text-align: center;
      }
      .carousel-slide span {
         left: 0;
      }
    }

    /* In your <style> block or css/home.css */
    #viewSiteBtn {
      background-color: #007BFF; /* Replace with the actual DC blue color */
      color: white;
      border: none; /* Remove default button border */
      padding: 15px 30px; /* Adjust padding as needed */
      font-size: 16px; /* Adjust font size as needed */
      cursor: pointer; /* Add a pointer cursor on hover */
      /* Add any other desired styles like rounded corners, etc. */
      border-radius: 5px; /* Example: Add rounded corners */
      margin-top: 10px;
    }

    #viewSiteBtn:hover {
      /* Optional: Add a hover effect, e.g., darken the blue */
      background-color: #0056b3; /* A slightly darker shade */
    }
    </style>
  </head>

  <body>
  <div class="container">
    <?php include 'navbar.php'; ?>
    <div class="main-carousel">
      <div class="carousel-wrapper">
        <div class="carousel-slide slide1">
          <span class="character-image" alt="Creature Commandos">
            <h2>Creature Commandos</h2>
            <h3>A ragtag team of monstrous heroes tackling missions too bizarre for the ordinary.</h3>
            <button>Get to know Creature Commandos</button>
          </span>
        </div>
        <div class="carousel-slide slide2">
          <span class="character-image" alt="Flash">
            <h2>The Flash</h2>
            <h3>The Fastest Man Alive, racing against time to save the multiverse!</h3>
            <button>Get to know The Flash</button>
          </span>
        </div>
        <div class="carousel-slide slide3">
          <span class="character-image" alt="Weasel">
            <h2>Weasel</h2>
            <h3>A creepy, unpredictable creature with questionable morals—but plenty of surprises!</h3>
            <button>Get to know Weasel</button>
          </span>
        </div>
      </div>
    </div>        
        <div class="content">
          <div class="section">
            <h2>SUICIDE SQUAD: KILL THE JUSTICE LEAGUE</h2>
            <!-- Suicide Squad: Kill the Justice League -->
            <div class="image-gallery">
              <div class="image-item">
                <img id="harley" src="images/harley.jpg" alt="Harley Quinn" class="character-image">
                <div class="image-name">Harley<br>Quinn</div>
              </div>
              <div class="image-item">
                <img id="boom" src="images/boomerang.jpg" alt="Captain Boomerang" class="character-image">
                <div class="image-name">Captain<br>Boomerang</div>
              </div>
              <div class="image-item">
                <img id="dead" src="images/deadshot.jpg" alt="Deadshot" class="character-image">
                <div class="image-name">Deadshot</div>
              </div>
              <div class="image-item">
                <img id="shark" src="images/shark.jpg" alt="King Shark" class="character-image">
                <div class="image-name">King Shark</div>
              </div>
            </div> <!-- End of .image-gallery (Suicide Squad) -->
          </div> <!-- End of .section -->
          <div class ="section">
            <div class="another">
              <h2>Character of the day</h2>
              <img id="reverse_flash" class="character-image" src="images/reverse_flash.jpg" alt="Reverse Flash">
              <h2>Reverse Flash</h2>
              <p>For every action, there's an equal and opposite reaction.
                 And with every step the Flash takes toward the future,
                  someone from the future is racing backward through time
                  to stop him—the villainous speedster known as the Reverse-Flash.</p>
            </div>
          
          </div>
          
          <div class="section">
            <h2>WHO'S WHO: THE JUSTICE LEAGUE</h2>
            <!-- Justice League -->
            <div class="image-gallery">
              <div class="image-item">
                <img id="superman" src="images/img5.jpg" alt="Superman" class="character-image">
                <div class="image-name">Superman</div>
              </div>
              <div class="image-item">
                <img id="batman" src="images/img6.jpg" alt="Batman" class="character-image">
                <div class="image-name">Batman</div>
              </div>
              <div class="image-item">
                <img id="wonder" src="images/img7.jpg" alt="Wonder Woman" class="character-image">
                <div class="image-name">Wonder<br>Woman</div>
              </div>
              <div class="image-item">
                <img id="greenlantern" src="images/img8.jpg" alt="Green Lantern" class="character-image">
                <div class="image-name">Green<br>Lantern</div>
              </div>
              <div class="image-item">
                <img id="flash" src="images/img9.jpg" alt="Flash" class="character-image">
                <div class="image-name">The Flash</div>
              </div>
              <div class="image-item">
                <img id="aquaman" src="images/img10.jpg" alt="Aquaman" class="character-image">
                <div class="image-name">Aquaman</div>
              </div>
              <div class="image-item">
                <img id="cyborg" src="images/img11.jpg" alt="Cyborg" class="character-image">
                <div class="image-name">Cyborg</div>
              </div>
            </div> <!-- End of .image-gallery (Justice League) -->
          </div> <!-- End of .section -->
          <div class="section" style="text-align: center;">
          <button id="viewSiteBtn" onclick="handleViewSiteClick()">Explore Characters</button>
          </div>
        </div> <!-- End of .content -->
        
        <?php include 'footer.php'; ?>
      </div> <!-- End of .container -->

<!-- Auth Modal -->
<div id="authModal" class="modal">
  <div class="modal-content">
      <span id="closeModal" class="close">&times;</span>
      <h2 id="modalTitle">Log In</h2>
      <div id="modalContent"></div>
  </div>
</div>

<script src="script/script.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
        fetch('script/checkSession.php')
        .then(res => res.json())
        .then(data => {
            if (!data.loggedIn) {
              openAuthModal("login");
            }
        })
        .catch(err => console.error("Session check error:", err));
      });
</script>
</body>

</html>
