<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {

    if (!isset($_FILES['xmlfile'])) {
        throw new Exception("No file uploaded.");
    }

    $xmlFile = $_FILES['xmlfile']['tmp_name'];

    if (!file_exists($xmlFile)) {
        throw new Exception("File not found.");
    }

    // Load XML safely
    libxml_use_internal_errors(true);
    $xml = simplexml_load_file($xmlFile);

    if ($xml === false) {
        throw new Exception("Invalid XML format.");
    }

    $inserted = 0;

    foreach ($xml->Contact as $contact) {

        // Extract safely
        $firstname   = trim((string)$contact->FirstName);
        $lastname    = trim((string)$contact->LastName);
        $phonenumber = trim((string)$contact->Phone->phonenumber);

        // Skip empty rows
        if (!$firstname || !$phonenumber) continue;

        // 🔒 OPTIONAL: prevent duplicates
        $checkSql = "SELECT COUNT(*) as count FROM dbo.grandstream_entry WHERE phonenumber = ?";
        $checkStmt = sqlsrv_query($conn, $checkSql, [$phonenumber]);
        $checkRow = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);

        if ($checkRow['count'] > 0) {
            continue; // skip duplicate
        }

        // Insert
        $sql = "INSERT INTO dbo.grandstream_entry
                (firstname, lastname, phonenumber, entry_date, entry_time)
                VALUES (?, ?, ?, GETDATE(), CONVERT(time, GETDATE()))";

        $params = [$firstname, $lastname, $phonenumber];

        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            throw new Exception(print_r(sqlsrv_errors(), true));
        }

        $inserted++;
    }

    $response['success'] = true;
    $response['message'] = "Imported $inserted contacts successfully.";

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>