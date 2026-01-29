<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $original_employee_id = trim($_POST['original_employee_id']);
    $name = trim($_POST['name']);
    $employee_id = trim($_POST['employee_id']);
    $department = trim($_POST['department']);
    $role = trim($_POST['role']);
    $status = trim($_POST['status']);

    // Validate that status is only 'Active' or 'Resigned'
    $allowed_statuses = ['Active', 'Resigned'];
    if (!in_array($status, $allowed_statuses)) {
        echo "<script>alert('Invalid status value.'); window.location.href='users_view.php';</script>";
        exit;
    }

    if ($name && $employee_id && $department && $role && $status) {
        $update_sql = "UPDATE itequip_inventory.users_profile 
                       SET name = ?, employee_id = ?, department = ?, role = ?, [status] = ? 
                       WHERE employee_id = ?";
        $params = [$name, $employee_id, $department, $role, $status, $original_employee_id];
        $stmt = sqlsrv_prepare($conn, $update_sql, $params);

        if ($stmt && sqlsrv_execute($stmt)) {
            echo "<script>alert('User updated successfully.'); window.location.href='users_view.php';</script>";
            exit;
        } else {
            echo "<script>alert('Failed to update user.'); window.location.href='users_view.php';</script>";
            exit;
        }
    }
}
?>
