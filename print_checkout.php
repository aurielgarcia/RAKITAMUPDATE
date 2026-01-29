<?php
include 'db_connect.php';

// Get the checkout ID from the URL
$checkout_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$equipmentName = $model = $serviceNowRequestTicket = $requestedBy = '';

if ($checkout_id > 0) {
    $sql = "SELECT equipment_name, model, service_now_request_ticket, requested_by 
            FROM itequip_inventory.checkouts WHERE id = ?";
    $params = array($checkout_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("<p class='error-message'>Database query error: " . print_r(sqlsrv_errors(), true) . "</p>");
    }

    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $equipmentName = $row['equipment_name'] ?? 'N/A';
        $model = $row['model'] ?? 'N/A';
        $serviceNowRequestTicket = $row['service_now_request_ticket'] ?? 'N/A';
        $requestedBy = $row['requested_by'] ?? 'N/A';

        $update_sql = "UPDATE itequip_inventory.checkouts SET print_status = 'printed' WHERE id = ?";
        $update_stmt = sqlsrv_query($conn, $update_sql, array($checkout_id));
        if ($update_stmt === false) {
            die("<p class='error-message'>Failed to update print status: " . print_r(sqlsrv_errors(), true) . "</p>");
        }

    } else {
        echo "<p class='error-message'>Checkout record not found.</p>";
        exit;
    }
} else {
    echo "<p class='error-message'>Invalid checkout ID.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Checkout</title>
    <link rel="stylesheet" href="styles.css">
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
            margin: 0 0 0 0;
        }

        .divider {
            height: 0.5pt;
            background-color: #000;
            margin: 1mm 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 6pt; /* smaller font size */
            margin: 0.3mm 0;
            white-space: nowrap; /* prevent wrapping */
            overflow: hidden;
        }

       .label, .value {
            font-weight: 800;
            color: #000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 5pt; /* match row size */
        }

        .label {
            width: 39%;
            padding-left: 3mm; /* Push the label to the right */
        }

        .value {
            text-align: left;
            width: 60%;
            word-break: break-word;
            padding-right: 2mm; /* Push the label to the right */
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
    <p>IT EQUIPMENT RELEASE TAG</p>

    <div class="divider"></div>

    <div class="info-row"><span class="label">USER:</span><span class="value"><?php echo htmlspecialchars($requestedBy); ?></span></div>
    <div class="info-row"><span class="label">ITEM NEEDED:</span><span class="value"><?php echo htmlspecialchars($equipmentName); ?></span></div>
    <div class="info-row"><span class="label">DEVICE MODEL:</span><span class="value"><?php echo htmlspecialchars($model); ?></span></div>
    <div class="info-row"><span class="label">TICKET:</span><span class="value"><?php echo htmlspecialchars($serviceNowRequestTicket); ?></span></div>

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
