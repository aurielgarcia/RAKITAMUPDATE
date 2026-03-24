<?php
include 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$joiners = $data['joiners'] ?? [];

$response = ['success' => false, 'message' => '', 'id' => null];

try {
    foreach ($joiners as $j) {

        $sqlInsert = "INSERT INTO dbo.it_onboarding 
            (employee_id, name, role, department, manager_name, joining_date, snow_ticket, pc_type, onboarding_status)
            OUTPUT INSERTED.id
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

        if ($stmtInsert === false) {
            throw new Exception('Insert failed: ' . json_encode(sqlsrv_errors(), JSON_PRETTY_PRINT));
        }

        // ✅ GET INSERTED ID
        if (sqlsrv_fetch($stmtInsert)) {
            $insertedId = sqlsrv_get_field($stmtInsert, 0);
            $response['id'] = $insertedId;
        }
    }

    $response['success'] = true;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);