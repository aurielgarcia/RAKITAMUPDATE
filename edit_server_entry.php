<?php
include 'db_connect.php';
$data = json_decode(file_get_contents('php://input'), true);
$entry = $data['entry'] ?? null;

$response = ['success' => false, 'message' => ''];

try {
    if(!$entry) throw new Exception('No entry data provided.');

    $sqlUpdate = "UPDATE dbo.server_room_entry
                  SET entry_date = ?, entry_time = ?, exit_time = ?, visitor_name = ?, department_company = ?, purpose = ?, authorized_by = ?
                  WHERE id = ?"
                  ;

    $params = [
        $entry['entry_date'],
        $entry['entry_time'],
        $entry['exit_time'],
        $entry['visitor_name'],
        $entry['department_company'],
        $entry['purpose'],
        $entry['authorized_by'],
        $entry['id']
    ];

    $stmt = sqlsrv_query($conn, $sqlUpdate, $params);
    if($stmt === false) throw new Exception('Update failed: '.print_r(sqlsrv_errors(), true));

    $response['success'] = true;

} catch(Exception $e){
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);