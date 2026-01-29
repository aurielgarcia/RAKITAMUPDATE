<?php
include('db_connect.php');

if (isset($_GET['name'])) {
    $name = $_GET['name'];
    $sql = "SELECT DISTINCT model FROM itequip_inventory.equipment WHERE name = ?";
    $stmt = sqlsrv_query($conn, $sql, [$name]);

    $options = "<option value=''>-- Select Model --</option>";
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $model = htmlspecialchars($row['model']);
            $options .= "<option value='$model'>$model</option>";
        }
        // Add this line to include the "Other" option
        $options .= "<option value='other'>Other (Type Manually)</option>";
    } else {
        $options = "<option value=''>Error loading models</option>";
    }
    echo $options;
} else {
    echo "<option value=''>Invalid request</option>";
}
?>
