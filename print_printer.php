<?php
include 'db_connect.php';

// Get the printer ID from the URL
$printer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$printerDescription = $printerModel = $printerSerialNumber = $printerLocation = '';

if ($printer_id > 0) {
    $sql = "SELECT printer_description, printer_model, printer_serialnumber, printer_location, print_status
            FROM dbo.printer_inventory
            WHERE id = ?";
    $params = array($printer_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("<p class='error-message'>Database query error: " . print_r(sqlsrv_errors(), true) . "</p>");
    }

    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $printerDescription = $row['printer_description'] ?? 'N/A';
        $printerModel = $row['printer_model'] ?? 'N/A';
        $printerSerialNumber = $row['printer_serialnumber'] ?? 'N/A';
        $printerLocation = $row['printer_location'] ?? 'N/A';

        // Update print status
        $update_sql = "UPDATE dbo.printer_inventory SET print_status = 'printed' WHERE id = ?";
        $update_stmt = sqlsrv_query($conn, $update_sql, array($printer_id));
        if ($update_stmt === false) {
            die("<p class='error-message'>Failed to update print status: " . print_r(sqlsrv_errors(), true) . "</p>");
        }

    } else {
        echo "<p class='error-message'>Printer record not found.</p>";
        exit;
    }
} else {
    echo "<p class='error-message'>Invalid printer ID.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Printer</title>
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
            margin: 0 auto 0; /* top 0, horizontal auto, bottom 0 */
            max-height: 6mm;
            filter: brightness(0.1);
        }

        p {
            font-size: 7pt;
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
            font-size: 6pt;
            margin: 0.3mm 0;
        }

        .label, .value {
            font-weight: 800;
            font-size: 5pt;
        }

        .label {
            width: 40%;
            padding-left: 3mm;
        }

        .value {
            width: 55%;
            padding-right: 2mm;
            padding-left: 4.5mm;
            white-space: nowrap;   /* prevent wrapping */
            overflow: hidden;      /* optional: hide overflow */
            text-overflow: ellipsis; /* optional: show ... if it overflows */
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
    <p>PRINTER ASSET TAG</p>

    <div class="divider"></div>
    <div class="info-row"><span class="label">DESCRIPTION:</span><span class="value"><?= htmlspecialchars($printerDescription) ?></span></div>
    <div class="info-row"><span class="label">MODEL:</span><span class="value"><?= htmlspecialchars($printerModel) ?></span></div>
    <div class="info-row"><span class="label">SERIAL NUMBER:</span><span class="value"><?= htmlspecialchars($printerSerialNumber) ?></span></div>
    <div class="info-row"><span class="label">LOCATION:</span><span class="value"><?= htmlspecialchars($printerLocation) ?></span></div>
    <div class="divider"></div>

    <script>
        function printAndStay() {
            window.print();
        }

        window.onafterprint = function () {
            if (window.opener && !window.opener.closed) {
                window.opener.location.reload();
            }
        };
    </script>
</body>
</html>
