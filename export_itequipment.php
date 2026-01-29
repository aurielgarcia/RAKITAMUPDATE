<?php
include 'db_connect.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=itequipment_inventory.xls");
header("Pragma: no-cache");
header("Expires: 0");

$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

$sql = "SELECT 
            name AS item_name, 
            model, 
            storage_location, 
            stock_quantity, 
            status AS item_status, 
            remarks 
        FROM itequip_inventory.equipment 
        ORDER BY stock_quantity $order";

$stmt = sqlsrv_query($conn, $sql);

echo "<table border='1'>";
echo "<tr>
        <th>Item Description</th>
        <th>Device Model</th>
        <th>Storage Location</th>
        <th>Stock Quantity</th>
      </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['model'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['storage_location']) . "</td>";
    echo "<td>" . ((int)$row['stock_quantity']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>