<?php
include 'db_connect.php';

// Get the asset ID from the URL
$asset_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$assignedTo = $deviceModel = $serialNumber = $serviceNowRequestTicket = '';

if ($asset_id > 0) {
    $sql = "SELECT assigned_to, device_model, serial_number, service_now_request_ticket
            FROM dbo.it_asset_inventory WHERE id = ?";
    $params = array($asset_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("<p class='error-message'>Database query error: " . print_r(sqlsrv_errors(), true) . "</p>");
    }

    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $assignedTo = $row['assigned_to'] ?? 'N/A';
        $deviceModel = $row['device_model'] ?? 'N/A';
        $serialNumber = $row['serial_number'] ?? 'N/A';
        $serviceNowRequestTicket = $row['service_now_request_ticket'] ?? 'N/A';

        $update_sql = "UPDATE dbo.it_asset_inventory SET print_status = 'printed' WHERE id = ?";
        $update_stmt = sqlsrv_query($conn, $update_sql, array($asset_id));
        if ($update_stmt === false) {
            die("<p class='error-message'>Failed to update print status: " . print_r(sqlsrv_errors(), true) . "</p>");
        }

    } else {
        echo "<p class='error-message'>Asset record not found.</p>";
        exit;
    }
} else {
    echo "<p class='error-message'>Invalid asset ID.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print IT Asset Checkout</title>
    <link rel="icon" type="image/png" href="favicon.jpg">
    <style>
        @page {
            size: 50mm 25mm;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 4mm;
            margin: 0;
            text-align: left;
            background: #fff;
            width: 50mm;
            height: 25mm;
            box-sizing: border-box;
        }

        .log {
            display: block;
            margin: 0 auto 0;
            max-height: 6mm;
            filter: brightness(0.1);
        }

        p {
            font-size: 6pt;
            font-weight: 900;
            text-align: center;
            margin: 0 0 1mm 0;
        }

        .divider {
            height: 0.5pt;
            background-color: #000;
            margin: 1mm 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 6pt;
            margin: 0.3mm 0;
            white-space: nowrap;
            overflow: hidden;
        }

        .label, .value {
            font-weight: 800;
            color: #000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 5pt;
        }

        .label {
            width: 39%;
            padding-left: 3mm;
        }

        .value {
            text-align: left;
            width: 60%;
            word-break: break-word;
            padding-right: 2mm;
        }

        .print-btn, .close-btn {
            display: none;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body onload="printAndStay()">
<img src="images/vertiv-print-logo1.png" alt="Vertiv Logo" class="log">
<p>IT ASSET RELEASE TAG</p>

<div class="divider"></div>

<div class="info-row"><span class="label">ASSIGNED TO:</span><span class="value"><?= htmlspecialchars($assignedTo) ?></span></div>
<div class="info-row"><span class="label">DEVICE MODEL:</span><span class="value"><?= htmlspecialchars($deviceModel) ?></span></div>
<div class="info-row"><span class="label">SERVICE TAG:</span><span class="value"><?= htmlspecialchars($serialNumber) ?></span></div>
<div class="info-row"><span class="label">TICKET:</span><span class="value"><?= htmlspecialchars($serviceNowRequestTicket) ?></span></div>

<div class="divider"></div>

 <script>
function printAndStay() {
    window.print();
    // stay on the print page after printing
}

window.onafterprint = function () {
    if (window.opener && !window.opener.closed) {
        window.opener.location.reload(); // Refresh the menu page automatically
    }
    // no redirect from the print page
};
    </script>
</body>
</html>
