<?php
include 'db_connect.php';

if (isset($_GET['serial_number'])) {
    $serial_number = $_GET['serial_number'];

    $sql = "SELECT item_description, device_model, device_type
            FROM dbo.it_asset_inventory 
            WHERE serial_number = ?";
    $params = array($serial_number);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        echo json_encode(['error' => 'Query failed']);
        exit;
    }

    $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(['error' => 'No serial number provided']);
}