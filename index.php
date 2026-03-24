<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: main_menu.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RAK | IT Management System</title>
  <link rel="icon" type="image/png" href="favicon.jpg">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      height: 100vh;
      background: linear-gradient(135deg, #1d1e33, #461959);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .auth-wrapper {
      backdrop-filter: blur(20px);
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      padding: 40px 30px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
      width: 350px;
      text-align: center;
      color: #fff;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .log {
      width: 200px;
      margin-bottom: 5px;
    }

    h2 {
      font-weight: 800;
      font-size: 1.7em;
      text-transform: uppercase;
      color: #ff7a00;
      margin-bottom: 20px;
      line-height: 1.2;
    }

    .form-wrapper input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      font-size: 0.95em;
      outline: none;
    }

    .form-wrapper input::placeholder {
      color: #ddd;
    }

    .btn-group {
      margin-top: 15px;
    }

    .btn {
      display: block;
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border: none;
      border-radius: 8px;
      background: linear-gradient(to right, #6a11cb, #2575fc);
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: transform 0.2s, background 0.3s;
      text-decoration: none;
      font-size: 14px;
      text-align: center;
    }

    .btn:hover {
      background: linear-gradient(to right, #2575fc, #6a11cb);
      transform: translateY(-2px);
    }

    .btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .forgot-password {
      margin-top: 15px;
    }

    .forgot-password a {
      color: #ccc;
      font-size: 0.85em;
      text-decoration: none;
    }

    .forgot-password a:hover {
      text-decoration: underline;
      color: #fff;
    }

    .message {
      background: rgba(255, 170, 170, 0.2);
      border: 1px solid #ffaaaa;
      color: #ffaaaa;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 0.85em;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.6);
      backdrop-filter: blur(8px);
    }

    .modal-content {
      background: #2c3e50;
      margin: 10% auto;
      padding: 30px;
      width: 90%;
      max-width: 450px;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.4);
      position: relative;
      color: white;
    }

    .close {
      position: absolute;
      right: 25px;
      top: 20px;
      font-size: 28px;
      cursor: pointer;
      color: #fff;
    }

    .modal-content h2 {
      margin-bottom: 20px;
      font-size: 1.5em;
    }

    .modal-content label {
      display: block;
      text-align: left;
      font-size: 12px;
      text-transform: uppercase;
      margin-bottom: 5px;
      color: #ff7a00;
      font-weight: 700;
    }
  </style>
</head>
<body>
  <div class="auth-wrapper">
    <img src="images/vertiv-logo2.png" alt="Vertiv Logo" class="log">
    <h2>RAK<br>IT MANAGEMENT SYSTEM</h2>

    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="message">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    ?>

    <div class="form-wrapper">
      <form action="user_auth.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        
        <div class="btn-group">
          <button type="submit" name="login" class="btn">Login</button>
          <button type="button" class="btn btn-secondary" id="regBtn">Register New Account</button>
        </div>

        <div class="forgot-password">
          <a href="forgot_password_form.php">Forgot Password?</a>
        </div>
      </form>
    </div>
  </div>

  <div id="regModal" class="modal">
    <div class="modal-content">
      <span class="close" id="closeBtn">&times;</span>
      <h2>Create Account</h2>
      <div class="form-wrapper">
        <form action="user_auth.php" method="POST">
          <div style="display: flex; gap: 10px;">
            <div style="flex: 1;">
              <label>First Name</label>
              <input type="text" name="first_name" required>
            </div>
            <div style="flex: 1;">
              <label>Last Name</label>
              <input type="text" name="last_name" required>
            </div>
          </div>

          <label>Employee ID</label>
          <input type="text" name="employee_id" required>
          
          <label>Username</label>
          <input type="text" name="username" required>
          
          <label>Password</label>
          <input type="password" name="password" required>
          
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" required>
          
          <button type="submit" name="register" class="btn" style="margin-top: 20px;">Register</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    const modal = document.getElementById('regModal');
    const btn = document.getElementById('regBtn');
    const span = document.getElementById('closeBtn');

    btn.onclick = function() {
      modal.style.display = "block";
    }

    span.onclick = function() {
      modal.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
</body>
</html>