<?php
session_start();
include('db_connect.php');

// Handle search and filters
$search = $_GET['search'] ?? '';
$year = $_GET['year'] ?? '';
$month = $_GET['month'] ?? '';

// Base query
$query = "SELECT * FROM itequip_inventory.toner_drum_checkout WHERE 1=1";
$params = [];

// Search filter
if (!empty($search)) {
    $query .= " AND (model LIKE ? OR color LIKE ? OR type LIKE ? OR requested_by LIKE ?)";
    $search_param = '%' . $search . '%';
    $params = array_merge($params, array_fill(0, 4, $search_param));
}

// Year filter
if (!empty($year)) {
    $query .= " AND YEAR(checkout_date) = ?";
    $params[] = $year;
}

// Month filter
if (!empty($month)) {
    $query .= " AND MONTH(checkout_date) = ?";
    $params[] = $month;
}

$query .= " ORDER BY checkout_date DESC";

$stmt = sqlsrv_prepare($conn, $query, $params);
sqlsrv_execute($stmt);

// Distinct years
$years_query = "SELECT DISTINCT YEAR(checkout_date) AS year FROM itequip_inventory.toner_drum_checkout WHERE checkout_date IS NOT NULL ORDER BY year DESC";
$years_result = sqlsrv_query($conn, $years_query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Printer Toner & Drum Check-out Records | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .table-wrapper {
    max-height: calc(100vh - 200px); /* Adjust depending on header/footer height */
    overflow-y: auto; /* vertical scroll when needed */
    overflow-x: auto; /* horizontal scroll when needed */
    border: 3px solid #ddd;
    width: 100%;
    box-sizing: border-box;
}

table#equipmentTable {
    min-width: 1000px; /* Prevents the table from shrinking too small */
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    background-color: white;
    table-layout: auto;
}

    table#equipmentTable th,
        table#equipmentTable td {
            padding: 10px;
            text-align: left;
            word-wrap: break-word;
            border: .5px solid #ccc; /* adds grid lines */
        }

table#equipmentTable th {
    position: sticky;
    top: 0;
    background-color: #343a40;
    color: white;
    z-index: 1;
}

        .color-box {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 1px solid #ccc;
            margin-right: 5px;
            vertical-align: middle;
        }

        table#equipmentTable th:nth-child(1),
        table#equipmentTable th:nth-child(2),
        table#equipmentTable th:nth-child(3),
        table#equipmentTable th:nth-child(4),
        table#equipmentTable th:nth-child(5),
        table#equipmentTable td:nth-child(5),
        table#equipmentTable th:nth-child(6) {
            text-align: center;
        }

        table#equipmentTable td:nth-child(4) {
            text-align: right;
        }

        th:nth-child(1), td:nth-child(1) { width: 5%; }
        th:nth-child(2), td:nth-child(2) { width: 5%; }
        th:nth-child(3), td:nth-child(3) { width: 5%; }
        th:nth-child(4), td:nth-child(4) { width: 5%; }
        th:nth-child(5), td:nth-child(5) { width: 5%; }
        th:nth-child(6), td:nth-child(6) { width: 10%; }

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="view_checkin_records_menu.php"><i class="material-icons">arrow_left</i>Stock Check-in/Check-out Menu</a>
    <a href="view_checkouts.php"><i class="material-icons">arrow_right</i>IT Equipment Stock Check-out Records</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="container">

<div class="filters">
    <form method="GET" class="search-form" style="text-align:center;">
        <input type="text" name="search" placeholder="Search model, color, type, requested by" value="<?= htmlspecialchars($search) ?>">

        <select name="year">
            <option value="">All Years</option>
            <?php while ($yr = sqlsrv_fetch_array($years_result, SQLSRV_FETCH_ASSOC)): ?>
                <option value="<?= $yr['year'] ?>" <?= ($year == $yr['year']) ? 'selected' : '' ?>><?= $yr['year'] ?></option>
            <?php endwhile; ?>
        </select>

        <select name="month">
            <option value="">All Months</option>
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>>
                    <?= date("F", mktime(0, 0, 0, $m, 1)) ?>
                </option>
            <?php endfor; ?>
        </select>

        <button type="submit">Filter</button>
    </form>
</div>

<div class="main-content">
    <div class="tab-header">Printer Toner & Drum Stock Check-out Records</div>

    <div class="table-wrapper">
        <table id="equipmentTable">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Color</th>
                    <th>Type</th>
                    <th>Checkout Date</th>
                    <th>Quantity Checked Out</th>
                    <th>Requested By</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($stmt): ?>
                    <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['model']) ?></td>
                            <td>
                                <div class="color-box" style="background-color: <?= htmlspecialchars($row['color']) ?>;"></div>
                                <?= htmlspecialchars($row['color']) ?>
                            </td>
                            <td><?= htmlspecialchars($row['type']) ?></td>
                            <td><?= $row['checkout_date'] ? $row['checkout_date']->format('M-d-Y') : '' ?></td>
                            <td><?= htmlspecialchars($row['quantity_checked_out']) ?></td>
                            <td><?= htmlspecialchars($row['requested_by']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No checkout records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

</body>
</html>
