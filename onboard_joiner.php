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

    // ✅ 1. Update onboarding using ID (MAIN KEY)
    $sqlUpdate = "UPDATE dbo.it_onboarding
                  SET onboarding_status = 'Onboarded'
                  WHERE id = ?";
    $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, [$joiner['id']]);

    if ($stmtUpdate === false) {
        throw new Exception('Update it_onboarding failed: ' . json_encode(sqlsrv_errors(), JSON_PRETTY_PRINT));
    }

    // ✅ 2. Clean joining date
    $joining_date = trim($joiner['joining_date'] ?? '');
    if ($joining_date === '' || strtoupper($joining_date) === 'TBC') {
        $joining_date = null;
    }

    // ✅ 3. Check using ID
    $sqlCheck = "SELECT COUNT(*) AS cnt 
                 FROM itequip_inventory.users_profile 
                 WHERE id = ?";
    $stmtCheck = sqlsrv_query($conn, $sqlCheck, [$joiner['id']]);

    if ($stmtCheck === false) {
        throw new Exception('Check failed: ' . json_encode(sqlsrv_errors(), JSON_PRETTY_PRINT));
    }

    $row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);

    // ✅ 4. Update or Insert
    if ($row['cnt'] > 0) {

        $sqlUpdateProfile = "UPDATE itequip_inventory.users_profile
                             SET employee_id = ?, name = ?, role = ?, department = ?, manager_name = ?, joining_date = ?
                             WHERE id = ?";

        $params = [
            $joiner['employee_id'],
            $joiner['name'],
            $joiner['role'],
            $joiner['department'],
            $joiner['manager_name'],
            $joining_date,
            $joiner['id']
        ];

        $stmtProfile = sqlsrv_query($conn, $sqlUpdateProfile, $params);

        if ($stmtProfile === false) {
            throw new Exception('Update users_profile failed: ' . json_encode(sqlsrv_errors(), JSON_PRETTY_PRINT));
        }

    } else {

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
            throw new Exception('Insert failed: ' . json_encode(sqlsrv_errors(), JSON_PRETTY_PRINT));
        }
    }

    $response['success'] = true;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>