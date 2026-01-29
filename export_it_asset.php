<?php
include 'db_connect.php';

// Set headers to force download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="it_asset_export.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV column headers
fputcsv($output, ['Serial Number', 'Install Status', 'Device Name', 'Device Model', 'Item Description', 'Purchase Date']);

// Prepare and execute query
$sql = "SELECT 
            serial_number, 
            install_status, 
            device_name, 
            device_model, 
            item_description, 
            purchased_date
        FROM itequip_inventory.dbo.it_asset_inventory
        WHERE install_status = 'In Stock - New'
        ORDER BY item_description ASC";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch and write rows
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Format purchase date if it's a DateTime object
    $purchaseDate = null;
    if (!empty($row['purchased_date'])) {
        if ($row['purchased_date'] instanceof DateTime) {
            $purchaseDate = $row['purchased_date']->format('Y-m-d');
        } else {
            $purchaseDate = $row['purchased_date'];
        }
    }

    fputcsv($output, [
        $row['serial_number'],
        $row['install_status'],
        $row['device_name'],
        $row['device_model'],
        $row['item_description'],
        $purchaseDate
    ]);
}

fclose($output);
exit;
?>
