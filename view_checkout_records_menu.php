<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Check-out Records | VERTIV GULF LLC ITMS</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="icon" type="image/png" href="favicon.jpg">
  <style>
    body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
        }

        #particles-js {
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        background-size: cover;
        background-position: center;
        z-index: 0;
    }

    .content {
        position: relative;
        z-index: 1;
        height: 90%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

        .sidebar {
            width: 230px;
            height: 100vh;
            background-color: #000;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            color: white;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #333;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }

        #welcomeMessage {
      margin-left: 20px;
      text-align: center;
      margin-top: 150px;
      color: #333;
      font-size: 20px;
    }

   
  </style>
</head>
<body>

<div id="particles-js"></div>

<div class="sidebar">
  <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
  <hr class="sidebar-divider">
  <a href="main_menu.php">> Main Menu</a>
  <a href="view_checkouts.php">> IT Equipment Stock Check-out Records</a>
  <a href="toner_drum_checkout_records.php">> Printer Toner & Drum Stock Check-out Records</a>
  <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="content">
  <div id="welcomeMessage">
    <h1>Welcome to Stock Check-out Records!</h1>
    <p>Select a category from the left sidebar to begin viewing your stock check-out records</p>
  </div>
  </div>


<!-- Particles.js library -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
  particlesJS("particles-js", {
    "particles": {
      "number": {
        "value": 80,
        "density": { "enable": true, "value_area": 800 }
      },
      "color": { "value": "#000000" },
      "shape": {
        "type": "circle",
        "stroke": { "width": 0, "color": "#000000" },
        "polygon": { "nb_sides": 5 }
      },
      "opacity": {
        "value": 0.3,
        "random": false,
        "anim": { "enable": false }
      },
      "size": {
        "value": 3,
        "random": true,
        "anim": { "enable": false }
      },
      "line_linked": {
        "enable": true,
        "distance": 150,
        "color": "#000000",
        "opacity": 0.2,
        "width": 1
      },
      "move": {
        "enable": true,
        "speed": 2,
        "direction": "none",
        "random": false,
        "straight": false,
        "out_mode": "out",
        "bounce": false
      }
    },
    "interactivity": {
      "detect_on": "canvas",
      "events": {
        "onhover": { "enable": true, "mode": "grab" },
        "onclick": { "enable": false },
        "resize": true
      },
      "modes": {
        "grab": { "distance": 140, "line_linked": { "opacity": 0.4 } }
      }
    },
    "retina_detect": true
  });
</script>

</body>
</html>
