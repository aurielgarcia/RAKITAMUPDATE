<?php
include 'db_connect.php';

$id = $_GET['id'];

$sql = "SELECT * FROM itequip_inventory.dbo.it_asset_inventory WHERE id = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);

if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Convert date fields for JSON (YYYY-MM-DD format)
    if (isset($row['deployed_date']) && $row['deployed_date'] instanceof DateTime) {
        $row['deployed_date'] = $row['deployed_date']->format('Y-m-d');
    }
    if (isset($row['purchased_date']) && $row['purchased_date'] instanceof DateTime) {
        $row['purchased_date'] = $row['purchased_date']->format('Y-m-d');
    }

    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Item not found']);
}