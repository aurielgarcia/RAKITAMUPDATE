<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deviceType = $_POST['deviceType'] ?? '';
    $requiredQty = (int)($_POST['requiredQty'] ?? 0);
    $username = $_SESSION['username'] ?? 'Unknown';

    if ($deviceType !== '') {

        // 🔍 Check if record exists
        $checkSql = "SELECT COUNT(*) as count FROM dbo.it_asset_summary WHERE device_type = ?";
        $checkStmt = sqlsrv_query($conn, $checkSql, [$deviceType]);
        $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);

        if ($row['count'] > 0) {
            // ✅ UPDATE
            $sql = "UPDATE dbo.it_asset_summary
                    SET required_quantity = ?, 
                        last_updated = GETDATE(),
                        last_updated_by = ?
                    WHERE device_type = ?";
            $params = [$requiredQty, $username, $deviceType];
        } else {
            // ✅ INSERT 
            $sql = "INSERT INTO dbo.it_asset_summary
                    (device_type, required_quantity, last_updated, last_updated_by)
                    VALUES (?, ?, GETDATE(), ?)";
            $params = [$deviceType, $requiredQty, $username];
        }

        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => sqlsrv_errors()
            ]);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    }
}
?>