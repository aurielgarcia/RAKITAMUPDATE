<?php
include 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$entries = $data['entries'] ?? [];

$response = ['success' => false, 'message' => ''];

if (sqlsrv_begin_transaction($conn) === false) {
    die(print_r(sqlsrv_errors(), true));
}

try {
    $sqlInsert = "INSERT INTO dbo.grandstream_entry 
                (firstname,lastname,phonenumber,entry_date,entry_time) 
                VALUES (?, ?, ?, ?, ?)";

    foreach ($entries as $e) {
        $paramsInsert = [
            $e['firstname'],
            $e['lastname'],
            $e['phonenumber'],
            $e['entry_date'],
            $e['entry_time'],
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