<?php
session_start();
require_once 'db_connect.php';

if (isset($_POST['register'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $full_name = $first_name . ' ' . $last_name;
    $employee_id = trim($_POST['employee_id']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match!";
        header("Location: index.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO itequip_inventory.users (name, employee_id, username, password) VALUES (?, ?, ?, ?)";
    $params = array($full_name, $employee_id, $username, $hashedPassword);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        $_SESSION['message'] = "Registration successful! Please login.";
    } else {
        $errors = sqlsrv_errors();
        $_SESSION['message'] = "Registration failed: " . $errors[0]['message'];
    }
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM itequip_inventory.users WHERE username = ?";
    $params = array($username);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $errors = sqlsrv_errors();
        $_SESSION['message'] = "Query Error: " . $errors[0]['message'];
        header("Location: index.php");
        exit();
    }

    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        header("Location: main_menu.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid username or password.";
        header("Location: index.php");
        exit();
    }
}

header("Location: index.php");
exit();