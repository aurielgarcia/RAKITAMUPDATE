<?php
include('db_connect.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $serial = $data['serial_number'] ?? '';
    $employee = $data['employee_name'] ?? '';
    $model = $data['device_model'] ?? '';
    $type = $data['device_type'] ?? '';

    if (!$serial || !$employee || !$model || !$type) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // 1. Update main asset table
    $update_sql = "
        UPDATE dbo.it_asset_inventory SET 
            install_status = 'In Stock - Spare',
            assigned_to = '',
            service_now_request_ticket = '',
            deployed_by = '',
            deployed_date = NULL,
            device_storage_location = 'I.T. Department Main Building',
            print_status = NULL
        WHERE serial_number = ?
    ";
    $update_stmt = sqlsrv_prepare($conn, $update_sql, [$serial]);

    if (!$update_stmt || !sqlsrv_execute($update_stmt)) {
        echo json_encode(['success' => false, 'message' => 'Failed to update asset_inventory']);
        exit;
    }

    // 2. Insert log into return_it_asset
    $insert_sql = "INSERT INTO dbo.return_it_asset (
    employee_name, device_model, device_type, serial_number, return_date
) VALUES (?, ?, ?, ?, CAST(GETDATE() AS DATE))";
    $insert_params = [$employee, $model, $type, $serial];
    $insert_stmt = sqlsrv_prepare($conn, $insert_sql, $insert_params);

    if (!$insert_stmt || !sqlsrv_execute($insert_stmt)) {
        echo json_encode(['success' => false, 'message' => 'Failed to log return']);
        exit;
    }

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
