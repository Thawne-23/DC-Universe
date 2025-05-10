<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us</title>
  <base href="/Dc_Characters/" />
  <link rel="icon" type="image/png" href="images/dc_logo.png">
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/about.css" />
  <link rel="stylesheet" href="css/modal.css" />
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />

  <style>
    .hero-banner {
      background: linear-gradient(
          to bottom,
          rgba(0, 0, 0, 0) 0%,
          rgba(0, 0, 0, 0.6) 50%,
          rgba(0, 0, 0, 0) 100%
        ),
        url('https://static.dc.com/2024-07/DC%20All%20In%20Announcement%20Banner_1.jpg') center/cover no-repeat;
      height: 300px;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      flex-direction: column;
      padding: 20px;
    }


    .hero-banner h1 {
      font-size: 3rem;
      text-shadow: 2px 2px 5px blue;
    }

    .hero-banner p {
      font-size: 1.2rem;
      max-width: 600px;
      margin-top: 10px;
      text-shadow: 1px 1px 3px blue;
    }

    .about-container {
      background-color: #ffffff;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: flex-start;
      gap: 40px;
      padding: 40px 30px;
      margin: 30px auto;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      max-width: 1100px;
    }

    .imgSection {
      flex: 1;
      min-width: 300px;
      max-width: 360px;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .imgSection img {
      width: 100%;
      height: 475px;
      object-fit: cover;
      display: block;
      border-radius: 12px;
    }

    .contentSection {
      flex: 1;
      min-width: 300px;
      max-width: 650px;
      background-color: #fafafa;
      border-radius: 12px;
      padding: 30px 35px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      color: #333;
      font-size: 1rem;
      line-height: 1.6;
    }

    .contentSection h1 {
      color: #0056b3;
      font-weight: 700;
      margin-bottom: 25px;
      font-size: 2.4rem;
    }

    .contentSection h3 {
      color: #2176bd;
      margin-top: 30px;
      margin-bottom: 15px;
      font-weight: 600;
      font-size: 1.6rem;
      border-bottom: 3px solid #2176bd;
      padding-bottom: 4px;
      display: inline-block;
    }

    .contentSection p,
    .contentSection ul {
      color: #444;
      font-size: 1rem;
    }

    .contentSection ul {
      list-style-type: disc;
      padding-left: 25px;
    }

    .dev-container {
      background-color: #ffffff;
      padding: 60px 20px;
      text-align: center;
      max-width: 1100px;
      margin: 60px auto 40px auto;
      border-radius: 12px;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
    }

    .dev-container h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 50px;
      color: #0056b3;
    }

    .dev-container h2 span {
      color: #2176bd;
    }

    .team-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 42px;
    }

    .profile {
      flex: 1 1 280px;
      max-width: 380px;
      background-color: #f9f9f9;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.07);
      transition: 0.3s;
    }

    .profile:hover {
      transform: translateY(-7px);
      box-shadow: 0 12px 30px rgba(33, 118, 189, 0.3);
    }

    .profile img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 20px;
    }

    .profile h3 {
      font-size: 1.6rem;
      font-weight: 700;
      color: #004080;
    }

    .profile .title {
      font-size: 1rem;
      color: #2176bd;
      margin-bottom: 10px;
    }

    .profile p {
      color: #444;
    }

    @media (max-width: 768px) {
      .about-container {
        flex-direction: column;
      }

      .imgSection img {
        height: auto;
      }

      .profile {
        max-width: 100%;
      }

      .hero-banner h1 {
        font-size: 2.2rem;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <?php include 'navbar.php'; ?>

    <section class="hero-banner" aria-label="Intro Banner">
      <h1>Discover the DC Multiverse</h1>
      <p>Learn the origins, powers, and stories behind your favorite heroes and villains.</p>
    </section>

    <div class="about-container" data-aos="fade-up">
      <div class="imgSection">
        <img src="images/justice.png" alt="Justice League" />
      </div>
      <div class="contentSection">
        <h1>About DC Universe</h1>
        <p>This site is your gateway to the vast DC Universe. Whether you're a fan of the Justice League, Teen Titans, or the Legion of Doom, we’ve got you covered.</p>
        <p>Each character profile is packed with detailed info, backstories, and unique insights curated by passionate developers and fans alike.</p>
        <h3>What You'll Find Here</h3>
        <ul>
          <li>In-depth character bios</li>
          <li>Power breakdowns and origin stories</li>
          <li>Connections to teams and story arcs</li>
        </ul>
      </div>
    </div>

    <div class="dev-container" data-aos="fade-up">
      <h2>Meet Our <span>Developers</span></h2>
      <div class="team-grid">
        <div class="profile" tabindex="0" title="Lead Developer">
          <img src="images/leo.png" alt="Leo Llarenas" />
          <h3>Leo Llarenas</h3>
          <div class="title">Lead Developer</div>
          <p>Leo steered the development and architecture of the project. From design to deployment, his guidance made this possible.</p>
        </div>
        <div class="profile" tabindex="0" title="UI/UX & Backend Specialist">
          <img src="images/justice.png" alt="Shanne Christopher Valdez" />
          <h3>Shanne Christopher Valdez</h3>
          <div class="title">Full-Stack Developer</div>
          <p>Shanne worked on the website design and back-end features, ensuring a user-friendly and responsive experience.</p>
        </div>
        <div class="profile" tabindex="0" title="Database & Interface Integrator">
          <img src="images/justice.png" alt="Edrian Orencia" />
          <h3>Edrian Orencia</h3>
          <div class="title">Full-Stack Developer</div>
          <p>Edrian focused on component efficiency and seamless Firestore integration to maintain high performance and scalability.</p>
        </div>
      </div>
    </div>  
  </div>

    <?php include 'footer.php'; ?>

    <div id="authModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalContent">
      <div class="modal-content">
        <span id="closeModal" class="close" aria-label="Close Modal">&times;</span>
        <h2 id="modalTitle">Log In</h2>
        <div id="modalContent"></div>
      </div>
    </div>

    <script src="script/script.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
      AOS.init({ duration: 800, once: true });
    </script>

</body>

</html>
