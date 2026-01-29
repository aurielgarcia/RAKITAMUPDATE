<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $printer_description = $_POST['printer_description'];
    $printer_model = $_POST['printer_model'];
    $printer_type = $_POST['printer_type'];
    $printer_serialnumber = $_POST['printer_serialnumber'];
    $printer_location = $_POST['printer_location'];
    $purchased_date = !empty($_POST['purchased_date']) ? $_POST['purchased_date'] : null;

    // New fields
    $printer_ip_address  = $_POST['printer_ip_address'] ?? null;
    $printer_subnet_mask = $_POST['printer_subnet_mask'] ?? null;
    $printer_gateway     = $_POST['printer_gateway'] ?? null;
    $printer_dns1        = $_POST['printer_dns1'] ?? null;
    $printer_dns2        = $_POST['printer_dns2'] ?? null;
    $printer_switch      = $_POST['printer_switch'] ?? null;
    $printer_port        = $_POST['printer_port'] ?? null;
    $printer_web_portal_admin_credentials = $_POST['printer_web_portal_admin_credentials'] ?? null;

    $sql = "
        UPDATE dbo.printer_inventory
        SET 
            printer_description = ?,
            printer_model = ?,
            printer_type = ?,
            printer_serialnumber = ?,
            printer_location = ?,
            purchased_date = ?,
            printer_ip_address = ?,
            printer_subnet_mask = ?,
            printer_gateway = ?,
            printer_dns1 = ?,
            printer_dns2 = ?,
            printer_switch = ?,
            printer_port = ?,
            printer_web_portal_admin_credentials = ?
        WHERE id = ?
    ";

    $params = [
        $printer_description,
        $printer_model,
        $printer_type,
        $printer_serialnumber,
        $printer_location,
        $purchased_date,
        $printer_ip_address,
        $printer_subnet_mask,
        $printer_gateway,
        $printer_dns1,
        $printer_dns2,
        $printer_switch,
        $printer_port,
        $printer_web_portal_admin_credentials,
        $id
    ];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        header("Location: printer_inventory.php?updated=1");
        exit();
    } else {
        echo "Error updating record.<br>";
        die(print_r(sqlsrv_errors(), true));
    }
} else {
    echo "Invalid request.";
}
?>
