<?php
include 'db_connect.php';
$data = json_decode(file_get_contents('php://input'), true);
$joiner = $data['joiner'] ?? null;

$response = ['success' => false, 'message' => ''];

try {
    if(!$joiner) throw new Exception('No joiner data provided.');

    $sqlUpdate = "UPDATE dbo.it_onboarding 
                  SET name = ?, role = ?, department = ?, manager_name = ?, joining_date = ?, snow_ticket = ?, pc_type = ?, onboarding_status = ?
                  WHERE employee_id = ?";

    $params = [
        $joiner['name'],
        $joiner['role'],
        $joiner['department'],
        $joiner['manager_name'],
        $joiner['joining_date'],
        $joiner['snow_ticket'],
        $joiner['pc_type'],
        $joiner['onboarding_status'],
        $joiner['employee_id']
    ];

    $stmt = sqlsrv_query($conn, $sqlUpdate, $params);
    if($stmt === false) throw new Exception('Update failed: '.print_r(sqlsrv_errors(), true));

    $response['success'] = true;

} catch(Exception $e){
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
