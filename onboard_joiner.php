<?php
include 'db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents('php://input'), true);
$joiner = $data['joiner'] ?? null;

$response = ['success' => false, 'message' => ''];

try {
    if (!$joiner || !isset($joiner['id'])) {
        throw new Exception('No joiner data or ID received.');
    }

    // ✅ CLEAN joining date
    $joining_date = trim($joiner['joining_date'] ?? '');
    if ($joining_date === '' || strtoupper($joining_date) === 'TBC') {
        $joining_date = null;
    }

    // =====================================================
    // ✅ NEW: CHECK DUPLICATE EMPLOYEE_ID BEFORE ONBOARDING
    // =====================================================
    $sqlCheckDuplicate = "SELECT COUNT(*) AS cnt 
                          FROM itequip_inventory.users_profile 
                          WHERE employee_id = ?";
    
    $stmtCheckDuplicate = sqlsrv_query($conn, $sqlCheckDuplicate, [$joiner['employee_id']]);

    if ($stmtCheckDuplicate === false) {
        throw new Exception('Duplicate check failed: ' . json_encode(sqlsrv_errors(), JSON_PRETTY_PRINT));
    }

    $rowDuplicate = sqlsrv_fetch_array($stmtCheckDuplicate, SQLSRV_FETCH_ASSOC);

    if ($rowDuplicate['cnt'] > 0) {
        // ❌ STOP IF DUPLICATE FOUND
        throw new Exception('This employee ID is already onboarded!');
    }

    // =====================================================
    // ✅ 1. UPDATE onboarding status
    // =====================================================
    $sqlUpdate = "UPDATE dbo.it_onboarding
                  SET onboarding_status = 'Onboarded'
                  WHERE id = ?";
    
    $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, [$joiner['id']]);

    if ($stmtUpdate === false) {
        throw new Exception('Update it_onboarding failed: ' . json_encode(sqlsrv_errors(), JSON_PRETTY_PRINT));
    }

    // =====================================================
    // ✅ 2. INSERT INTO users_profile (NO NEED TO CHECK ID NOW)
    // =====================================================
    $sqlInsertProfile = "INSERT INTO itequip_inventory.users_profile
        (employee_id, name, role, department, manager_name, joining_date, status)
        VALUES (?, ?, ?, ?, ?, ?, 'Active')";

    $params = [
        $joiner['employee_id'],
        $joiner['name'],
        $joiner['role'],
        $joiner['department'],
        $joiner['manager_name'],
        $joining_date
    ];

    $stmtInsert = sqlsrv_query($conn, $sqlInsertProfile, $params);

    if ($stmtInsert === false) {
        throw new Exception('Insert failed: ' . json_encode(sqlsrv_errors(), JSON_PRETTY_PRINT));
    }

    $response['success'] = true;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>