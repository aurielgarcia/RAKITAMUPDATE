<?php
include('db_connect.php'); // Connect to SQL Server

header('Content-Type: application/json'); // Ensure proper JSON response

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $employee_id = trim($_POST['employee_id'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $status = trim($_POST['status'] ?? '');

    // Input validation
    if ($name && $employee_id && $department && $role && $status) {
        $sql = "INSERT INTO itequip_inventory.users_profile (name, employee_id, department, role, status)
                VALUES (?, ?, ?, ?, ?)";
        $params = array($name, $employee_id, $department, $role, $status);

        $stmt = sqlsrv_prepare($conn, $sql, $params);

        if ($stmt && sqlsrv_execute($stmt)) {
            echo json_encode([
                "success" => true,
                "message" => "User added successfully."
            ]);
        } else {
            $error = sqlsrv_errors();
            echo json_encode([
                "success" => false,
                "message" => "Database error.",
                "details" => $error
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}
?>
