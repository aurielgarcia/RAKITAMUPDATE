<?php
session_start();
include 'db_connect.php'; // Ensure this connects using sqlsrv

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['message'] = "<p class='alert error'>Passwords do not match.</p>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ? WHERE username = ?";
        $params = array($hashed_password, $username);
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt && sqlsrv_rows_affected($stmt) > 0) {
            $_SESSION['message'] = "<p class='alert success'>Password updated successfully!</p>";
        } else {
            $_SESSION['message'] = "<p class='alert error'>User not found or update failed.</p>";
        }
    }

    header("Location: index.php");
    exit();
}
?>