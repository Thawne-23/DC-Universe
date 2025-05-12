<!DOCTYPE html>
<html lang="en">
<head>
    <title>DC Page</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/navbar.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
</head>
<body>
<header class="header">
  <div class="navigator-container">
    <div class="nav">
      <div class="headerButtons">
        <a href="webpages/home.php"><img id="DC_Logo" src="images/dc_logo.png" alt="DC Logo"></a>
        <div class="menu-toggle" onclick="toggleMenu()">☰</div>
      </div>
      <ul id="nav-menu">
        <li><a href="webpages/home.php">HOME</a></li>
        <li><a href="webpages/about.php">ABOUT US</a></li>
        <li><a href="webpages/contact.php">CONTACT US</a></li>
        <li><a href="webpages/help.php">HELP</a></li>
      </ul>
      <ul class="register">
        <li><a href="javascript:void(0);" id="logIn">Log In</a></li>
        <li><a href="javascript:void(0);" id="signUp">Sign Up</a></li>
      </ul>
    </div>
  </div>
</header>

<script>
  function toggleMenu() {
    document.getElementById("nav-menu").classList.toggle("open");
  }
  
</script>

</body>
</html>
