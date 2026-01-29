<?php
session_start();
include 'db_connect.php'; // Uses sqlsrv_connect()

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset'])) {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['message'] = "<p style='color:red;'>Passwords do not match.</p>";
        header("Location: forgot_password_form.php");
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Use full table name with schema
    $sql = "UPDATE itequip_inventory.users SET password = ? WHERE username = ?";
    $params = array($hashed_password, $username);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $_SESSION['message'] = "<p style='color:red;'>Error executing query:<br>" . print_r(sqlsrv_errors(), true) . "</p>";
    } elseif (sqlsrv_rows_affected($stmt) > 0) {
        $_SESSION['message'] = "<p style='color:limegreen;'>Password successfully updated.</p>";
        sqlsrv_free_stmt($stmt);
    } else {
        $_SESSION['message'] = "<p style='color:red;'>Username not found.</p>";
        sqlsrv_free_stmt($stmt);
    }

    header("Location: forgot_password_form.php");
    exit();
}
?>
