<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <base href="/Dc_Characters/">
  <link rel="icon" type="image/png" href="images/dc_logo.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/modal.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>Contact Us</title>
  <style>
    .contact-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 40px;
      padding: 40px 20px;
      margin-top: 70px;
      background: #f5f5f5;
    }

    form {
      flex: 1;
      min-width: 320px;
      max-width: 500px;
      background: #ffffff;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      animation: slideUp 0.5s ease;
    }

    form p {
      margin-bottom: 20px;
      font-size: 18px;
    }

    label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }

    input[type="text"],
    input[type="email"],
    textarea {
      width: 100%;
      padding: 12px 14px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }

    textarea {
      resize: vertical;
    }

    #message-count {
      float: right;
      font-size: 12px;
      color: #888;
    }

    button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background-color: #007BFF;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #0056b3;
    }

    .get-in-touch {
      flex: 1;
      min-width: 280px;
      max-width: 400px;
      background: white;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      animation: fadeIn 1s ease;
    }

    .get-in-touch h3 {
      margin-top: 0;
      font-size: 22px;
    }

    .get-in-touch p {
      margin: 10px 0;
    }

    .social-media {
      margin: 15px 0;
    }

    .social-media a {
      display: inline-block;
      text-decoration: none;
      color: white;
      background-color: #3b5998;    
      padding: 8px 10px;
      border-radius: 6px;
      transition: background 0.3s ease;
    }

    .social-media a:hover {
      opacity: 0.9;
    }

    .social-media a i {
      margin-right: 6px;
    }

    #mail a {
      color: #007BFF;
      text-decoration: underline;
    }

    .map-preview {
      margin-top: 15px;
      border-radius: 10px;
      overflow: hidden;
    }

    .map-preview iframe {
      width: 100%;
      height: 200px;
      border: none;
    }

    @keyframes slideUp {
      from { transform: translateY(20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @media screen and (max-width: 768px) {
      .contact-container {
        flex-direction: column;
        align-items: center;
      }

      form, .get-in-touch {
        max-width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <?php include 'navbar.php'; ?>

    <!-- Contact Us Section -->
    <div class="contact-container">
      <form onsubmit="return submitContactForm()">
        <p><i class="fas fa-paper-plane"></i> Have questions or want to connect? Fill out the form below!</p>

        <label for="name"><i class="fas fa-user"></i> Name:</label>
        <input type="text" id="name" name="name" required aria-label="Name">

        <label for="email"><i class="fas fa-envelope"></i> Email:</label>
        <input type="email" id="email" name="email" required aria-label="Email">

        <label for="message"><i class="fas fa-comment"></i> Message: <span id="message-count">0/500</span></label>
        <textarea id="message" name="message" rows="5" maxlength="500" required aria-label="Message"></textarea>

        <button type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
      </form>

      <div class="get-in-touch">
        <h3><i class="fas fa-handshake"></i> Get in Touch</h3>
        <p>We appreciate your interest in DC characters! Feel free to reach out through our social media:</p>
        <div class="social-media">
          <a href="https://x.com/dcofficial?lang=en" aria-label="Twitter"><i class="fab fa-twitter"></i> Twitter</a>
          <a href="https://www.facebook.com/DCAsiaOfficial" aria-label="Facebook"><i class="fab fa-facebook"></i> Facebook</a>
          <a href="https://www.instagram.com/dcofficial?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" aria-label="Instagram"><i class="fab fa-instagram"></i> Instagram</a>
        </div>
        <p id="mail">You can also email us at <a href="mailto:leollarenas23@gmail.com">https://support.dcuniverse.com/hc/en-us​.</a>.</p>

        <div class="map-preview">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13207.407114986501!2d-118.35625101268833!3d34.15013402258452!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80c2bfc91b7bf323%3A0x16d1cd7dc89a5826!2sWarner%20Bros.%20Studios%2C%20Burbank%2C%20CA%2C%20USA!5e0!3m2!1sen!2sph!4v1746366766494!5m2!1sen!2sph" 
            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        </div>
      </div>
    </div>

    <?php include 'footer.php'; ?>
  </div>

  <!-- Auth Modal -->
  <div id="authModal" class="modal">
    <div class="modal-content">
      <span id="closeModal" class="close">&times;</span>
      <h2 id="modalTitle">Log In</h2>
      <div id="modalContent"></div>
    </div>
  </div>

  <script>
    const message = document.getElementById('message');
    const count = document.getElementById('message-count');

    message.addEventListener('input', () => {
      count.textContent = `${message.value.length}/500`;
    });

    function submitContactForm() {
      alert("✅ Thank you for reaching out! We'll get back to you soon.");
      return true;
    }
  </script>

  <script src="script/script.js"></script>
</body>
</html>
