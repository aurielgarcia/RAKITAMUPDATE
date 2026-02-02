<?php
include 'db_connect.php';

// Read JSON payload
$data = json_decode(file_get_contents('php://input'), true);
$joiners = $data['joiners'] ?? [];

$response = ['success' => false, 'message' => ''];

try {
    foreach ($joiners as $j) {
        // Insert into dbo.it_onboarding only
        $sqlInsert = "INSERT INTO dbo.it_onboarding 
            (employee_id, name, role, department, manager_name, joining_date, snow_ticket, pc_type, onboarding_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $paramsInsert = [
            $j['employee_id'],
            $j['name'],
            $j['role'],
            $j['department'],
            $j['manager_name'],
            $j['joining_date'],
            $j['snow_ticket'],
            $j['pc_type'],
            $j['onboarding_status']
        ];
        $stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);
        if ($stmtInsert === false) throw new Exception('Insert into it_onboarding failed.');
    }

    $response['success'] = true;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
