<?php
include 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$entries = $data['entries'] ?? [];

$response = ['success' => false, 'message' => ''];

if (sqlsrv_begin_transaction($conn) === false) {
    die(print_r(sqlsrv_errors(), true));
}

try {
    $sqlInsert = "INSERT INTO dbo.server_room_entry 
                (entry_date, entry_time, exit_time, visitor_name, department_company, purpose, authorized_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

    foreach ($entries as $e) {
        $paramsInsert = [
            $e['entry_date'],
            $e['entry_time'],
            $e['exit_time'] ?? null,
            $e['visitor_name'],
            $e['department_company'],
            $e['purpose'],
            $e['authorized_by']
        ];

        $stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);
        
        if ($stmtInsert === false) {
            throw new Exception('Insert failed: ' . print_r(sqlsrv_errors(), true));
        }
    }

    sqlsrv_commit($conn);
    $response['success'] = true;

} catch (Exception $ex) {
    sqlsrv_rollback($conn);
    $response['message'] = $ex->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);