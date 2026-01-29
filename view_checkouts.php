<?php
include 'db_connect.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';

// Get distinct years
$yearQuery = "SELECT DISTINCT YEAR(checkout_date) AS year FROM itequip_inventory.checkouts ORDER BY year DESC";
$yearResult = sqlsrv_query($conn, $yearQuery);

// Search and filter query
$conditions = [];
$params = [];
$sql = "SELECT c.id, c.equipment_id, e.model, c.equipment_name, c.checkout_date, 
               c.quantity_checked_out, c.requested_by, c.service_now_request_ticket, c.reason_for_request 
        FROM itequip_inventory.checkouts c
        LEFT JOIN itequip_inventory.equipment e ON c.equipment_id = e.id
        WHERE (c.equipment_name LIKE ? OR 
               CONVERT(varchar, c.checkout_date, 23) LIKE ? OR 
               c.requested_by LIKE ? OR 
               c.service_now_request_ticket LIKE ? OR
               e.model LIKE ?)";

$searchWildcard = '%' . $search . '%';
$params[] = [$searchWildcard, SQLSRV_PARAM_IN];
$params[] = [$searchWildcard, SQLSRV_PARAM_IN];
$params[] = [$searchWildcard, SQLSRV_PARAM_IN];
$params[] = [$searchWildcard, SQLSRV_PARAM_IN];
$params[] = [$searchWildcard, SQLSRV_PARAM_IN];

if (!empty($selectedYear)) {
    $sql .= " AND YEAR(checkout_date) = ?";
    $params[] = [$selectedYear, SQLSRV_PARAM_IN];
}

if (!empty($selectedMonth)) {
    $sql .= " AND MONTH(checkout_date) = ?";
    $params[] = [$selectedMonth, SQLSRV_PARAM_IN];
}

$sql .= " ORDER BY checkout_date DESC";

// Flatten $params
$flatParams = [];
foreach ($params as $param) {
    $flatParams[] = $param[0];
}

$stmt = sqlsrv_query($conn, $sql, $flatParams);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Equipment Stock Check-out Records | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .table-wrapper {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            overflow-x: auto;
            border: 3px solid #ddd;
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
            background-color: white;
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
        table#equipmentTable th:nth-child(5),
        table#equipmentTable td:nth-child(5),
        table#equipmentTable th:nth-child(6),
        table#equipmentTable th:nth-child(7) {
            text-align: center;
        }

        table#equipmentTable td:nth-child(4) {
            text-align: right;
        }

        th:nth-child(1), td:nth-child(1) { width: 15%; }
        th:nth-child(2), td:nth-child(2) { width: 15%; }
        th:nth-child(3), td:nth-child(3) { width: 10%; }
        th:nth-child(4), td:nth-child(4) { width: 10%; }
        th:nth-child(5), td:nth-child(5) { width: 5%; }
        th:nth-child(6), td:nth-child(6) { width: 15%; }
        th:nth-child(7), td:nth-child(7) { width: 8%; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="view_checkin_records_menu.php"><i class="material-icons">arrow_left</i>Stock Check-in/Check-out Menu</a>
    <a href="toner_drum_checkout_records.php"><i class="material-icons">arrow_right</i>Printer Toner & Drum Stock Check-out Records</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="container">
    <form method="GET" class="search-form" style="text-align:center;">
        <input type="text" name="search" placeholder="Search by Item, Date, Requester or Ticket..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="year">
            <option value="">Filter by Year</option>
            <?php while ($row = sqlsrv_fetch_array($yearResult, SQLSRV_FETCH_ASSOC)): ?>
                <option value="<?php echo $row['year']; ?>" <?php if ($selectedYear == $row['year']) echo 'selected'; ?>>
                    <?php echo $row['year']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <select name="month">
            <option value="">Filter by Month</option>
            <?php
            for ($m = 1; $m <= 12; $m++) {
                $monthName = date('F', mktime(0, 0, 0, $m, 10));
                $selected = ($selectedMonth == $m) ? 'selected' : '';
                echo "<option value='$m' $selected>$monthName</option>";
            }
            ?>
        </select>
        <button type="submit">Search</button>
    </form>

    <div class="main-content">
        <div class="tab-header">IT Equipment Stock Check-out Records</div>

        <div class="table-wrapper">
            <table id="equipmentTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Item Description</th>
                        <th>Device Model</th>
                        <th>Check-Out Date</th>
                        <th>Check-Out Quantity</th>
                        <th>ServiceNow Ticket</th>
                        <th>Reason for Request</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($stmt && sqlsrv_has_rows($stmt)): ?>
                        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['requested_by']); ?></td>
                                <td><?php echo htmlspecialchars($row['equipment_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['model'] ?? ''); ?></td>
                                <td><?php echo $row['checkout_date'] instanceof DateTime ? $row['checkout_date']->format('M-d-Y') : ''; ?></td>
                                <td><?php echo $row['quantity_checked_out']; ?></td>
                                <td><?php echo htmlspecialchars($row['service_now_request_ticket'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['reason_for_request']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="no-records">No checkout records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
