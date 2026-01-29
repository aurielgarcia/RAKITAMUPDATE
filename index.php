<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RAK | IT Management System</title>
  <link rel="icon" type="image/png" href="favicon.jpg">

  <!-- Google Fonts -->
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
      width: 320px;
      text-align: center;
      color: #fff;
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
    }

    .form-wrapper input {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      font-size: 0.95em;
    }

    .form-wrapper input::placeholder {
      color: #ddd;
    }

    .btn-group {
      margin-top: 15px;
    }

    .btn {
      width: 100%;
      padding: 10px;
      margin: 5px 0;
      border: none;
      border-radius: 8px;
      background: linear-gradient(to right, #6a11cb, #2575fc);
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn:hover {
      background: linear-gradient(to right, #2575fc, #6a11cb);
    }

    .btn-secondary {
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid #fff;
    }

    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.3);
    }

    .forgot-password {
      margin-top: 10px;
    }

    .forgot-password a {
      color: #ccc;
      font-size: 0.9em;
      text-decoration: none;
    }

    .forgot-password a:hover {
      text-decoration: underline;
    }

    .message {
      color: #ffaaaa;
      margin-bottom: 10px;
      font-size: 0.9em;
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
      <form action="user_auth.php" method="POST" class="auth-form">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
  </div>
        <div class="btn-group">
          <button type="submit" name="login" class="btn">Login</button>
          <button type="submit" name="register" class="btn btn-secondary">Register</button>
        </div>

        <div class="forgot-password">
          <a href="forgot_password_form.php">Forgot Password?</a>
        </div>
      </form>
    </div>
</body>
</html>