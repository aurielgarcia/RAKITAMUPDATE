<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $item_description = $_POST['item_description'];
    $device_model = $_POST['device_model'];
    $serial_number = $_POST['serial_number'];
    $device_processor = $_POST['device_processor'];
    $device_storage = $_POST['device_storage'];
    $device_ram = $_POST['device_ram'];
    $device_gpu = $_POST['device_gpu'];
    $device_type = $_POST['device_type'];
    $install_status = $_POST['install_status'];
    $deployed_by = $_POST['deployed_by'];
    $deployed_date = !empty($_POST['deployed_date']) ? $_POST['deployed_date'] : null;
    $purchased_date = !empty($_POST['purchased_date']) ? $_POST['purchased_date'] : null; // ✅ Added this
    $assigned_to = $_POST['assigned_to'];
    $service_now_request_ticket = $_POST['service_now_request_ticket'];
    $device_storage_location = $_POST['device_storage_location'];
    $remarks = $_POST['remarks'];

    // ✅ Added purchased_date to the query
    $sql = "
        UPDATE itequip_inventory.dbo.it_asset_inventory
        SET 
            item_description = ?,
            device_model = ?,
            serial_number = ?,
            device_processor = ?,
            device_storage = ?,
            device_ram = ?,
            device_gpu = ?,
            device_type = ?,
            install_status = ?,
            deployed_by = ?,
            deployed_date = ?,
            purchased_date = ?, -- ✅ Added here
            assigned_to = ?,
            service_now_request_ticket = ?,
            device_storage_location = ?,
            remarks = ?
        WHERE id = ?
    ";

    // ✅ Added $purchased_date in correct position
    $params = [
        $item_description,
        $device_model,
        $serial_number,
        $device_processor,
        $device_storage,
        $device_ram,
        $device_gpu,
        $device_type,
        $install_status,
        $deployed_by,
        $deployed_date,
        $purchased_date, // ✅ Added here
        $assigned_to,
        $service_now_request_ticket,
        $device_storage_location,
        $remarks,
        $id
    ];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        header("Location: it_asset_inventory.php?updated=1");
        exit();
    } else {
        echo "Error updating record.<br>";
        die(print_r(sqlsrv_errors(), true));
    }
} else {
    echo "Invalid request.";
}
?>
