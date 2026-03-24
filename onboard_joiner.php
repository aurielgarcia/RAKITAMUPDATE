<?php
include 'db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents('php://input'), true);
$joiner = $data['joiner'] ?? null;

$response = ['success' => false, 'message' => ''];

try {
    if (!$joiner) {
        throw new Exception('No joiner data received.');
    }

    // ✅ 1. Update dbo.it_onboarding to Onboarded
    $sqlUpdate = "UPDATE dbo.it_onboarding
                  SET onboarding_status = 'Onboarded'
                  WHERE employee_id = ?";
    $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, [$joiner['employee_id']]);
    if ($stmtUpdate === false) {
        $errors = sqlsrv_errors();
        throw new Exception('Update it_onboarding failed: ' . json_encode($errors, JSON_PRETTY_PRINT));
    }

    // ✅ 2. Clean joining date
    $joining_date = trim($joiner['joining_date'] ?? '');
    if ($joining_date === '' || strtoupper($joining_date) === 'TBC') {
        $joining_date = null;
    }

    // ✅ 3. Check if record exists in itequip_inventory.users_profile
    $sqlCheck = "SELECT COUNT(*) AS cnt FROM itequip_inventory.users_profile WHERE employee_id = ?";
    $stmtCheck = sqlsrv_query($conn, $sqlCheck, [$joiner['employee_id']]);
    if ($stmtCheck === false) {
        $errors = sqlsrv_errors();
        throw new Exception('Check failed: ' . json_encode($errors, JSON_PRETTY_PRINT));
    }

    $row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
    if (!$row) {
        throw new Exception('Check query returned no rows.');
    }

    // ✅ 4. Update or insert into users_profile
    if ($row['cnt'] > 0) {
        // Update
        $sqlUpdateProfile = "UPDATE itequip_inventory.users_profile
                             SET name = ?, role = ?, department = ?, manager_name = ?, joining_date = ?
                             WHERE employee_id = ?";
        $params = [
            $joiner['name'],
            $joiner['role'],
            $joiner['department'],
            $joiner['manager_name'],
            $joining_date,
            $joiner['employee_id']
        ];
        $stmtProfile = sqlsrv_query($conn, $sqlUpdateProfile, $params);
        if ($stmtProfile === false) {
            $errors = sqlsrv_errors();
            throw new Exception('Update users_profile failed: ' . json_encode($errors, JSON_PRETTY_PRINT));
        }
    } else {
        // Insert
        $sqlInsertProfile = "INSERT INTO itequip_inventory.users_profile
            (employee_id, name, role, department, manager_name, joining_date)
            VALUES (?, ?, ?, ?, ?, ?)";
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
            $errors = sqlsrv_errors();
            throw new Exception('Insert into users_profile failed: ' . json_encode($errors, JSON_PRETTY_PRINT));
        }
    }

    $response['success'] = true;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
