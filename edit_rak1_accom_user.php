<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $employee_name = $_POST['employee_name'];
    $employee_id_input = $_POST['employee_id_input'];
    $mac_address1 = $_POST['mac_address1'];
    $mac_address2 = $_POST['mac_address2'];

    $update_query = "UPDATE dbo.rak1_accom_users_mac
                     SET employee_name = ?, employee_id = ?, mac_address1 = ?, mac_address2 = ?
                     WHERE employee_id = ?";
    $stmt = sqlsrv_query($conn, $update_query, [$employee_name, $employee_id_input, $mac_address1, $mac_address2, $employee_id]);

    if ($stmt) {
        header("Location: view_accom_user_records.php?status=updated");
        exit();
    } else {
        header("Location: view_accom_user_records.php?status=error");
        exit();
    }
} else {
    header("Location: view_accom_user_records.php");
    exit();
}
?>