<?php
$servername = "AE-D-J6HLW34\SQLEXPRESS"; // Double backslash is correct
$username = "admin";                        // Make sure this user exists and has access
$password = "Password@12345678";              // Replace with your actual password
$database = "itequip_inventory";         // Your actual DB name

// Connect to SQL Server
$conn = sqlsrv_connect($servername, array(
    "Database" => $database,
    "UID" => $username,
    "PWD" => $password,
    "TrustServerCertificate" => true    // Prevent SSL trust errors
));

// Check connection
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
