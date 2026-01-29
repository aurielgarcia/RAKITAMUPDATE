<?php
include 'db_connect.php';

if (isset($_POST['item_description'])) {
    $item_description = trim($_POST['item_description']);

    $sql = "SELECT DISTINCT device_model 
            FROM itequip_inventory.dbo.it_asset_inventory 
            WHERE LTRIM(RTRIM(item_description)) = LTRIM(RTRIM(?)) AND device_model IS NOT NULL AND device_model != ''";
    
    $stmt = sqlsrv_query($conn, $sql, [$item_description]);

    if ($stmt === false) {
        echo '<option value="">Database error: ' . htmlspecialchars(print_r(sqlsrv_errors(), true)) . '</option>';
        exit;
    }

    echo '<option value="">-- Select Device Model --</option>';
    $hasRows = false;
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $hasRows = true;
        $model = trim($row['device_model']);
        echo "<option value='" . htmlspecialchars($model) . "'>" . htmlspecialchars($model) . "</option>";
    }

    if (!$hasRows) {
        echo '<option value="">No models found for "' . htmlspecialchars($item_description) . '"</option>';
    }
} else {
    echo '<option value="">No item description received</option>';
}
