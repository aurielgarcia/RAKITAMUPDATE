<?php
include 'db_connect.php';
session_start(); // Make sure you have session started for user tracking

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deviceType = $_POST['deviceType'] ?? '';
    $requiredQty = (int)($_POST['requiredQty'] ?? 0);
    $username = $_SESSION['username'] ?? 'Unknown'; // Replace with your login system username

    if ($deviceType !== '') {
        $sql = "UPDATE dbo.it_asset_summary
                SET required_quantity = ?, 
                    last_updated = GETDATE(),
                    last_updated_by = ?
                WHERE device_type = ?";
        $params = [$requiredQty, $username, $deviceType];
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => sqlsrv_errors()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    }
}
?>
