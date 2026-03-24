<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if (!isset($conn)) {
    echo json_encode(["error" => "Database connection not available"]);
    exit;
}

// ✅ FIXED: include ID
$sql = "SELECT 
            id,
            employee_id,
            name,
            role,
            department,
            manager_name,
            joining_date,
            snow_ticket,
            pc_type,
            onboarding_status
        FROM dbo.it_onboarding
        WHERE onboarding_status IN ('New Joiner','Existing - Approved','Now Onboarding')
        ORDER BY name ASC";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    echo json_encode(["error" => "Query failed", "details" => sqlsrv_errors()]);
    exit;
}

$joiners = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    if ($row['joining_date'] instanceof DateTime) {
        $row['joining_date'] = $row['joining_date']->format('Y-m-d');
    }
    $joiners[] = $row;
}

echo json_encode($joiners);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>