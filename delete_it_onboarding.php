<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

// Changed from employee_id to id
if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unique ID is required']);
    exit;
}

$db_id = $data['id'];

// ✅ Delete record using the unique ID
$sql = "DELETE FROM dbo.it_onboarding WHERE id = ?";
$params = [$db_id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    // Helpful for debugging: return the actual SQL error
    $errors = sqlsrv_errors();
    echo json_encode(['success' => false, 'message' => 'Failed to delete: ' . json_encode($errors)]);
    exit;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo json_encode(['success' => true]);
?>