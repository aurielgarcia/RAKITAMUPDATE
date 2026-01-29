<?php
include('db_connect.php');

if (isset($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];

    // Delete query
    $delete_query = "DELETE FROM dbo.rak1_accom_users_mac WHERE employee_id = ?";
    $stmt = sqlsrv_query($conn, $delete_query, [$employee_id]);

    if ($stmt) {
        // Redirect back with success message
        header("Location: view_accom_user_records.php?status=deleted");
        exit();
    } else {
        // Redirect back with error message
        header("Location: view_accom_user_records..php?status=error");
        exit();
    }
} else {
    // No employee_id provided
    header("Location: view_accom_user_records..php");
    exit();
}
?>
