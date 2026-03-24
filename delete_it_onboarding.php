<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID is required']);
    exit;
}

$id = $data['id'];

// ✅ DELETE ONLY ONE ROW USING UNIQUE ID
$sql = "DELETE FROM dbo.it_onboarding WHERE id = ?";
$params = [$id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete record',
        'error' => print_r(sqlsrv_errors(), true)
    ]);
    exit;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo json_encode(['success' => true]);
?>