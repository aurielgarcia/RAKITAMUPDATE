<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>VERTIV GULF LLC ITMS | MAIN MENU</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="icon" type="image/png" href="favicon.jpg">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Roboto', sans-serif;
      overflow-x: hidden;
      overflow-y: auto;
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
  max-width: 1200px;
  padding: 40px 20px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-sizing: border-box;
}

    h1 {
      text-align: center;
      font-size: 2.5rem;
      max-width: 90vw;
      word-wrap: break-word;
      color: #d2691e;
      margin-bottom: 50px;
      text-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .menu-boxes {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 70px; /* 2 inches apart */
  max-width: 100%;
  margin-top: 20px;
  box-sizing: border-box;
}

    .menu-box {
      flex: 1 1 220px;
      max-width: 240px;
      height: 240px;
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      align-items: center;
      text-align: center;
      font-weight: bold;
      font-size: 1rem;
      color: #333;
      text-decoration: none;
      transition: all 0.3s ease;
      padding: 15px;
      box-sizing: border-box;
    }

    .menu-box:hover {
      background-color: #ffffff;
      transform: scale(1.08);
    }

    .menu-box img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 10px;
    }

    .logout {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #d9534f;
      color: white;
      padding: 10px 15px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      z-index: 9999;
    }

    .logout:hover {
      background: #c9302c;
    }

    @media (max-width: 600px) {
      .menu-box {
        max-width: 100%;
        height: auto;
      }
      .menu-box img {
        height: auto;
      }
      h1 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

<div id="particles-js"></div>

<div class="content">
  <a href="logout.php" class="logout">Logout</a>
  <h1>VERTIV GULF<br>IT MANAGEMENT SYSTEM</h1>

  <div class="menu-boxes">
    <a href="it_onboarding.php" class="menu-box">
      <img src="images/it_onboarding.jpg" alt="Available IT Asset">
      IT Onboarding Device Requirements
    </a>

    <a href="it_asset_count.php" class="menu-box">
      <img src="images/it_assets.jpg" alt="Available IT Asset">
      IT ASSET COUNT
    </a>

    <a href="inventory_menu.php" class="menu-box">
      <img src="images/it_equipment.jpg" alt="IT Inventory">
      IT INVENTORY
    </a>

    <a href="stock_checkin_menu.php" class="menu-box">
      <img src="images/stock_checkin.jpg" alt="Stock Check-In">
      STOCK CHECK-IN
    </a>

    <a href="stock_checkout_menu.php" class="menu-box">
      <img src="images/stock_checkout.jpg" alt="Stock Check-out">
      STOCK CHECK-OUT
    </a>

    <a href="view_checkin_records_menu.php" class="menu-box">
      <img src="images/stock_checkin__checkout_records.jpg" alt="View Check-in Records">
      STOCK CHECK-IN / CHECK-OUT RECORDS
    </a>

    <a href="users_view.php" class="menu-box">
      <img src="images/users_view.jpg" alt="Users View">
      USERS VIEW
    </a>

    <a href="view_accom_user_records.php" class="menu-box">
      <img src="images/wifi_accom.jpg" alt="WIFI Accommodation User Management & Records">
      WIFI ACCOMMODATION USER MANAGEMENT & RECORDS
    </a>
  </div>
</div>

<!-- Particles.js library -->
<script src="js/particles.min.js"></script>
<script>
  particlesJS("particles-js", {
    "particles": {
      "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
      "color": { "value": "#000000" },
      "shape": { "type": "circle", "stroke": { "width": 0, "color": "#000000" }, "polygon": { "nb_sides": 5 } },
      "opacity": { "value": 0.3, "random": false, "anim": { "enable": false } },
      "size": { "value": 3, "random": true, "anim": { "enable": false } },
      "line_linked": { "enable": true, "distance": 150, "color": "#000000", "opacity": 0.2, "width": 1 },
      "move": { "enable": true, "speed": 2, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false }
    },
    "interactivity": {
      "detect_on": "canvas",
      "events": { "onhover": { "enable": true, "mode": "grab" }, "onclick": { "enable": false }, "resize": true },
      "modes": { "grab": { "distance": 140, "line_linked": { "opacity": 0.4 } } }
    },
    "retina_detect": true
  });
</script>

</body>
</html>
