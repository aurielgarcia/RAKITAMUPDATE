<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // SQL Server parameterized delete query
    $sql = "DELETE FROM itequip_inventory.equipment WHERE id = ?";
    $params = array($id);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        echo "Error deleting record: ";
        die(print_r(sqlsrv_errors(), true));
    } else {
        // Redirect back to the Dashboard
        header("Location: dashboard.php");
        exit;
    }
}
?>

