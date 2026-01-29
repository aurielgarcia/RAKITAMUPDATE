<?php
session_start();
include 'db_connect.php';

// Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ✅ Check if the username already exists
    $check_sql = "SELECT id FROM itequip_inventory.users WHERE username = ?";
    $check_stmt = sqlsrv_prepare($conn, $check_sql, array(&$username));
    
    if (sqlsrv_execute($check_stmt)) {
        $check_result = sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC);
        if ($check_result) {
            $_SESSION['message'] = "<p class='alert error'>User is already registered.</p>";
        } else {
            // Proceed to insert new user
            $insert_sql = "INSERT INTO itequip_inventory.users (username, password) VALUES (?, ?)";
            $insert_stmt = sqlsrv_prepare($conn, $insert_sql, array(&$username, &$password));

            // Add error checking on execution
            if (sqlsrv_execute($insert_stmt)) {
                $_SESSION['message'] = "<p class='alert success'>Registration successful!</p>";
            } else {
                $errors = sqlsrv_errors();
                $_SESSION['message'] = "<p class='alert error'>Error executing insert query: " . print_r($errors, true) . "</p>";
            }
        }
    } else {
        $_SESSION['message'] = "<p class='alert error'>Error checking user existence: " . print_r(sqlsrv_errors(), true) . "</p>";
    }

    // Redirect back to the index page after registration
    header("Location: index.php");
    exit();
}

// Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ✅ Query to check if the user exists and fetch the password
    $login_sql = "SELECT id, password FROM itequip_inventory.users WHERE username = ?";
    $login_stmt = sqlsrv_prepare($conn, $login_sql, array(&$username));
    if (sqlsrv_execute($login_stmt)) {
        $user = sqlsrv_fetch_array($login_stmt, SQLSRV_FETCH_ASSOC);
        if ($user) {
            // Check if the password is correct
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                header("Location: main_menu.php");
                exit();
            } else {
                $_SESSION['message'] = "<p class='alert error'>Invalid password.</p>";
            }
        } else {
            $_SESSION['message'] = "<p class='alert error'>User not found.</p>";
        }
    } else {
        $_SESSION['message'] = "<p class='alert error'>Error executing login query: " . print_r(sqlsrv_errors(), true) . "</p>";
    }

    // Redirect back to login page with error
    header("Location: index.php");
    exit();
}
?>
