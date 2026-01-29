<?php
include 'db_connect.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$sql = "SELECT * FROM dbo.printer_inventory WHERE id = ?";
$params = [$id];

$stmt = sqlsrv_query($conn, $sql, $params);
if (!$stmt) {
    echo json_encode(['error' => sqlsrv_errors()]);
    exit;
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($row) {
    if (isset($row['purchased_date']) && $row['purchased_date'] instanceof DateTime) {
        $row['purchased_date'] = $row['purchased_date']->format('Y-m-d');
    } else {
        $row['purchased_date'] = '';
    }
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Item not found']);
}
?>
