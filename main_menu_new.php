<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit;
}
$username = $_SESSION['username']; // Get the logged-in user
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VERTIV GULF LLC ITMS | MAIN MENU</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="favicon.jpg">
  
  <style>
    :root {
        --primary-color: #d2691e;
        --danger-color: #d9534f;
        --text-dark: #2d3436;
        --glass-bg: rgba(255, 255, 255, 0.85);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Inter', sans-serif;
      background-color: #f4f7f6; /* Fallback */
      color: var(--text-dark);
    }

    #particles-js {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: 0;
    }

    /* Top Navigation Bar */
    .top-bar {
      position: fixed;
      top: 0;
      width: 100%;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding: 15px 40px;
      z-index: 1000;
      box-sizing: border-box;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(5px);
    }

    .user-profile {
      margin-right: 20px;
      font-weight: 600;
      color: var(--text-dark);
      background: white;
      padding: 8px 15px;
      border-radius: 50px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      font-size: 0.9rem;
    }

    .logout-btn {
      background: var(--danger-color);
      color: white;
      padding: 8px 20px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.9rem;
      transition: var(--transition);
      box-shadow: 0 4px 10px rgba(217, 83, 79, 0.3);
    }

    .logout-btn:hover {
      background: #c9302c;
      transform: translateY(-2px);
    }

    .content {
      position: relative;
      z-index: 1;
      max-width: 1300px;
      padding: 100px 20px 60px;
      margin: 0 auto;
      text-align: center;
    }

    h1 {
      font-size: 2.2rem;
      font-weight: 800;
      color: var(--primary-color);
      letter-spacing: -0.5px;
      margin-bottom: 60px;
      text-transform: uppercase;
    }

    h1 span {
        color: #333;
        font-weight: 300;
        display: block;
        font-size: 1.2rem;
        letter-spacing: 4px;
        margin-bottom: 5px;
    }

    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 40px;
      padding: 20px;
    }

    .menu-card {
      background: var(--glass-bg);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 20px;
      padding: 20px;
      text-decoration: none;
      color: var(--text-dark);
      transition: var(--transition);
      display: flex;
      flex-direction: column;
      align-items: center;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .menu-card:hover {
      transform: translateY(-10px);
      background: #ffffff;
      box-shadow: 0 20px 40px rgba(0,0,0,0.12);
      border-color: var(--primary-color);
    }

    .image-container {
      width: 100%;
      height: 160px;
      overflow: hidden;
      border-radius: 12px;
      margin-bottom: 15px;
    }

    .menu-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: var(--transition);
    }

    .menu-card:hover img {
      transform: scale(1.1);
    }

    .menu-card span {
      font-weight: 700;
      font-size: 0.95rem;
      line-height: 1.4;
      margin-top: 10px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
      .top-bar { padding: 10px 20px; }
      h1 { font-size: 1.6rem; }
      .menu-grid { gap: 20px; }
    }
  </style>
</head>
<body>

<div id="particles-js"></div>

<header class="top-bar">
    <div class="user-profile">👤 <?php echo htmlspecialchars($username); ?></div>
    <a href="logout.php" class="logout-btn">Logout</a>
</header>

<div class="content">
  <h1><span>VERTIV GULF</span>IT Management System</h1>

  <div class="menu-grid">
    <a href="it_onboarding.php" class="menu-card">
      <div class="image-container">
        <img src="images/it_onboarding.jpg" alt="IT Onboarding">
      </div>
      <span>IT Onboarding Requirements</span>
    </a>

    <a href="it_asset_count.php" class="menu-card">
      <div class="image-container">
        <img src="images/it_assets.jpg" alt="IT Asset Count">
      </div>
      <span>IT Asset Count</span>
    </a>

    <a href="inventory_menu.php" class="menu-card">
      <div class="image-container">
        <img src="images/it_equipment.jpg" alt="IT Inventory">
      </div>
      <span>IT Inventory</span>
    </a>

    <a href="stock_checkin_menu.php" class="menu-card">
      <div class="image-container">
        <img src="images/stock_checkin.jpg" alt="Stock Check-In">
      </div>
      <span>Stock Check-In</span>
    </a>

    <a href="stock_checkout_menu.php" class="menu-card">
      <div class="image-container">
        <img src="images/stock_checkout.jpg" alt="Stock Check-Out">
      </div>
      <span>Stock Check-Out</span>
    </a>

    <a href="view_checkin_records_menu.php" class="menu-card">
      <div class="image-container">
        <img src="images/stock_checkin__checkout_records.jpg" alt="Records">
      </div>
      <span>Check-In/Out Records</span>
    </a>

    <a href="users_view.php" class="menu-card">
      <div class="image-container">
        <img src="images/users_view.jpg" alt="Users View">
      </div>
      <span>Users View</span>
    </a>

    <a href="view_accom_user_records.php" class="menu-card">
      <div class="image-container">
        <img src="images/wifi_accom.jpg" alt="WiFi Management">
      </div>
      <span>WiFi Accommodation Records</span>
    </a>

    <a href="server_room_entry.php" class="menu-card">
      <div class="image-container">
        <img src="images/server_room_entry.jpg" alt="Server Room">
      </div>
      <span>Server Room Register</span>
    </a>

    <a href="it_knowledge.php" class="menu-card">
      <div class="image-container">
        <img src="images/knowledge_based.png" alt="IT Knowledge">
      </div>
      <span>IT Knowledge Base</span>
    </a>
    
  </div>
</div>

<script src="js/particles.min.js"></script>
<script>
  particlesJS("particles-js", {
    "particles": {
      "number": { "value": 60, "density": { "enable": true, "value_area": 1000 } },
      "color": { "value": "#d2691e" },
      "shape": { "type": "circle" },
      "opacity": { "value": 0.2 },
      "size": { "value": 3, "random": true },
      "line_linked": { "enable": true, "distance": 150, "color": "#d2691e", "opacity": 0.1, "width": 1 },
      "move": { "enable": true, "speed": 1.5 }
    },
    "interactivity": {
      "events": { "onhover": { "enable": true, "mode": "grab" } }
    },
    "retina_detect": true
  });
</script>

</body>
</html>