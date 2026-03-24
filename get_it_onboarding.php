<?php
header('Content-Type: application/json');

// Include your existing database connection
require_once 'db_connect.php';

// Check if $conn exists
if (!isset($conn)) {
    echo json_encode(["error" => "Database connection not available"]);
    exit;
}

// Fetch active joiners only
$sql = "SELECT employee_id, name, role, department, manager_name, joining_date, snow_ticket, pc_type, onboarding_status
        FROM dbo.it_onboarding
        WHERE onboarding_status IN ('New Joiner','Existing - Approved','Now Onboarding')
        ORDER BY name ASC";

$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    echo json_encode(["error" => "Query failed"]);
    exit;
}

$joiners = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Convert joining_date to string if it's a DateTime object
    if ($row['joining_date'] instanceof DateTime) {
        $row['joining_date'] = $row['joining_date']->format('Y-m-d');
    }
    $joiners[] = $row;
}

// Return JSON
echo json_encode($joiners);

// Free statement and close connection
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
