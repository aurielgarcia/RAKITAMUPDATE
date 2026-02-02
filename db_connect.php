<?php
$servername = "AE-L-9CQ4TQ3\\SQLEXPRESS"; // Double backslash is correct
$username   = "admin";                     // SQL login username
$password   = "Password@12345678";        // SQL login password
$database   = "itequip_inventory";        // Your database name

// Connect to SQL Server
$conn = sqlsrv_connect($servername, array(
    "Database" => $database,
    "UID" => $username,
    "PWD" => $password,
    "TrustServerCertificate" => true    // Prevent SSL trust errors
));

// Check connection
if ($conn === false) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
} else {
    // Connection successful
}
?>
