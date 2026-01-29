<?php
include 'db_connect.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM itequip_inventory.dbo.it_asset_inventory WHERE id = ?";
    $stmt = sqlsrv_prepare($conn, $sql, array(&$id));

    if (sqlsrv_execute($stmt)) {
        echo "Deleted";
    } else {
        echo "Error";
    }
}
?>