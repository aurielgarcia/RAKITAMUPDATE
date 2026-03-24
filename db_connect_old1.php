<?php
// Remote SQL Server info
$servername = "172.16.14.81"; // Double backslash is correct
$username = "itequip_inventory_admin";                        // Make sure this user exists and has access
$password = "Password@12345678";              // Replace with your actual password
$database = "itequip_inventory";         // Your actual DB name

// Connection options
$connectionOptions = array(
    "Database" => $database,
    "UID" => $username,
    "PWD" => $password,
    "TrustServerCertificate" => true,   // Prevent SSL trust errors
    "CharacterSet" => "UTF-8"           // Optional: ensure proper encoding
);

// Connect to SQL Server
$conn = sqlsrv_connect($servername, $connectionOptions);

// Check connection
if ($conn === false) {
    echo "Connection failed!<br>";
    die(print_r(sqlsrv_errors(), true));
} else {
    echo "Connection successful!";
}

?>
