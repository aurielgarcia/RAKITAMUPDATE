<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - RAK IT System</title>
  
  <link rel="icon" type="image/png" href="favicon.jpg">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@800&display=swap" rel="stylesheet">

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

    h2 {
      color: #ff7a00;
      font-size: 2em;
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
      font-family: sans-serif;
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

    .message {
      margin-top: 15px;
      color: white;
    }
  </style>
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-container">
      <h2>Reset Password</h2>

      <?php
      if (isset($_SESSION['message'])) {
          echo '<div class="message">' . $_SESSION['message'] . '</div>';
          unset($_SESSION['message']);
      }
      ?>
    <div class="form-wrapper">
      <form action="reset_password.php" method="POST">
        <input type="text" name="username" placeholder="Enter your username" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required><br><br>
        <button type="submit" name="reset" class="btn">Update Password</button>
      </form>
      </div>
      
      <a href="index.php" class="btn btn-secondary" style="display: inline-block; text-decoration: none; text-align: center; font-size: 0.95em;">Back to Login</a>
    
    </div>
  </div>
</body>
</html>
