<?php
header('Content-Type: application/json');

require_once 'db_connect.php';

if (!isset($conn)) {
    echo json_encode(["error" => "Database connection not available"]);
    exit;
}

$sql = "SELECT id, entry_date, entry_time, exit_time, visitor_name, department_company, purpose, authorized_by
        FROM dbo.server_room_entry
        ORDER BY entry_date DESC, entry_time DESC";

$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    $errors = sqlsrv_errors();
    echo json_encode(["error" => "Query failed: " . $errors[0]['message']]);
    exit;
}

$entries = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    if ($row['entry_date'] instanceof DateTime) {
        $row['entry_date'] = $row['entry_date']->format('Y-m-d');
    }
    if ($row['entry_time'] instanceof DateTime) {
        $row['entry_time'] = $row['entry_time']->format('H:i:s');
    }
    if ($row['exit_time'] instanceof DateTime) {
        $row['exit_time'] = $row['exit_time']->format('H:i:s');
    }
    
    $entries[] = $row;
}

echo json_encode($entries);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>