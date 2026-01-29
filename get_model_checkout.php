<?php
include('db_connect.php');

// Check if 'name' is passed in the request
if (isset($_GET['name'])) {
    $name = $_GET['name'];

    // Query to fetch distinct models with stock for the selected equipment name
    $sql = "SELECT DISTINCT model 
            FROM itequip_inventory.equipment 
            WHERE name = ? AND stock_quantity > 0 AND model IS NOT NULL AND model != ''
            ORDER BY model ASC";

    $stmt = sqlsrv_query($conn, $sql, [$name]);

    $options = "<option value=''>-- Select Model --</option>"; // Default option
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $model = htmlspecialchars($row['model']);
            $options .= "<option value='$model'>$model</option>";
        }
    } else {
        $options = "<option value=''>-- Error loading models --</option>";
    }

    echo $options;
} else {
    echo "<option value=''>-- Invalid request --</option>";
}
?>
