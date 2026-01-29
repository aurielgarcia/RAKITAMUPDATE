<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['printer_description'];
    $model = $_POST['printer_model'];
    $type = $_POST['printer_type'];
    $serial = $_POST['printer_serialnumber'];
    $location = $_POST['printer_location'];
    $purchased = !empty($_POST['purchased_date']) ? $_POST['purchased_date'] : null;

    // New fields
    $ip_address   = $_POST['printer_ip_address'] ?? null;
    $subnet_mask  = $_POST['printer_subnet_mask'] ?? null;
    $gateway      = $_POST['printer_gateway'] ?? null;
    $dns1         = $_POST['printer_dns1'] ?? null;
    $dns2         = $_POST['printer_dns2'] ?? null;
    $switch       = $_POST['printer_switch'] ?? null;
    $port         = $_POST['printer_port'] ?? null;
    $credentials  = $_POST['printer_web_portal_admin_credentials'] ?? null;

    $sql = "INSERT INTO dbo.printer_inventory 
        (printer_description, printer_model, printer_type, printer_serialnumber, printer_location, purchased_date,
         printer_ip_address, printer_subnet_mask, printer_gateway, printer_dns1, printer_dns2,
         printer_switch, printer_port, printer_web_portal_admin_credentials)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $description,
        $model,
        $type,
        $serial,
        $location,
        $purchased,
        $ip_address,
        $subnet_mask,
        $gateway,
        $dns1,
        $dns2,
        $switch,
        $port,
        $credentials
    ];
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    header("Location: printer_inventory.php"); // redirect back
    exit;
}
?>
