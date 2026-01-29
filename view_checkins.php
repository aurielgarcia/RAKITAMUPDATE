<?php
include 'db_connect.php';

// Fetch all check-in records (adjust table/schema name if needed)
$sql = "SELECT name, model, order_date, quantity_ordered, delivery_date, quantity_delivered, status, supplier FROM itequip_inventory.checkins ORDER BY delivery_date DESC";

$stmt = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Equipment Stock Check-in Records | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .table-wrapper {
            max-height: calc(112vh - 200px);
            overflow-y: auto;
            overflow-x: auto;
            border: 2px solid #ddd;
            width: 100%;
            box-sizing: border-box;
        }

        table#equipmentTable th {
            position: sticky;
            top: 0;
            background-color: #343a40;
            color: white;
            z-index: 1;
        }

        table#equipmentTable {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            table-layout: auto;
        }

        table#equipmentTable th,
        table#equipmentTable td {
            padding: 10px;
            text-align: left;
            word-wrap: break-word;
            border: .5px solid #ccc; /* adds grid lines */
        }

        table#equipmentTable th:nth-child(1),
        table#equipmentTable th:nth-child(2),
        table#equipmentTable th:nth-child(3),
        table#equipmentTable th:nth-child(4),
        table#equipmentTable td:nth-child(4),
        table#equipmentTable th:nth-child(5),
        table#equipmentTable th:nth-child(6),
        table#equipmentTable td:nth-child(6),
        table#equipmentTable th:nth-child(7),
        table#equipmentTable th:nth-child(8) {
            text-align: center;
        }

        table#equipmentTable td:nth-child(3),
        table#equipmentTable td:nth-child(5) {
            text-align: right;
        }

        th:nth-child(1), td:nth-child(1) { width: 25%; }
        th:nth-child(2), td:nth-child(2) { width: 15%; }
        th:nth-child(3), td:nth-child(3) { width: 15%; }
        th:nth-child(4), td:nth-child(4) { width: 10%; }
        th:nth-child(5), td:nth-child(5) { width: 15%; }
        th:nth-child(6), td:nth-child(6) { width: 10%; }
        th:nth-child(6), td:nth-child(7) { width: 8%; }
        th:nth-child(6), td:nth-child(8) { width: 8%; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="view_checkin_records_menu.php"><i class="material-icons">arrow_left</i>Stock Check-in/Check-out Menu</a>
    <a href="toner_drum_checkin_records.php"><i class="material-icons">arrow_right</i> Printer Toner & Drum Check-in Records</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">IT Equipment Stock Check-in Records</div>

    <div class="table-wrapper">
        <table id="equipmentTable">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Device Model</th>
                    <th>Order Date</th>
                    <th>Quantity Ordered</th>
                    <th>Delivery Date</th>
                    <th>Quantity Delivered</th>
                    <th>Status</th>
                    <th>Supplier</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($stmt): ?>
                    <?php
                    $hasRows = false;
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $hasRows = true;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['model']); ?></td>
                            <td><?= $row['order_date'] ? $row['order_date']->format('M-d-Y') : 'N/A'; ?></td>
                            <td><?= $row['quantity_ordered']; ?></td>
                            <td><?= $row['delivery_date'] ? $row['delivery_date']->format('M-d-Y') : 'N/A'; ?></td>
                            <td><?= $row['quantity_delivered'] ?? '0'; ?></td>
                            <td><?= htmlspecialchars($row['status']); ?></td>
                            <td><?= htmlspecialchars($row['supplier']); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (!$hasRows): ?>
                        <tr><td colspan="7" class="no-records">No check-in records found.</td></tr>
                    <?php endif; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="no-records">Query failed.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
