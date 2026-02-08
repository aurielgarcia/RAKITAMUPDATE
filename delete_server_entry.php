<?php
include 'db_connect.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$response = ['success' => false, 'message' => ''];

if (isset($data['entry_id'])) {
    $id = $data['entry_id'];
    
    $sql = "DELETE FROM dbo.server_room_entry WHERE id = ?";
    $params = array($id);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        $response['success'] = true;
    } else {
        $errors = sqlsrv_errors();
        $response['message'] = "SQL Error: " . $errors[0]['message'];
    }
} else {
    $response['message'] = "No ID provided.";
}

header('Content-Type: application/json');
echo json_encode($response);
?>