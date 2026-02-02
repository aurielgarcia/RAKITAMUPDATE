<?php
include 'db_connect.php';

// ✅ Fetch only Onboarded records
$sql = "SELECT employee_id, name, role, department, manager_name, joining_date, snow_ticket, pc_type, onboarding_status, created_at, updated_at
        FROM dbo.it_onboarding
        WHERE LOWER(onboarding_status) = 'onboarded'
        ORDER BY created_at DESC";

$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Onboarded Employees | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow-y: auto;
        }

        .table-wrapper {
            height: calc(100.9vh - 200px);
            overflow-y: auto;
            overflow-x: auto;
            border: 2px solid #ddd;
            box-sizing: border-box;
            width: 100%;
        }

        .main-content {
            margin-left: 220px;
            padding: 20px;
        }

        .tab-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 0;
            text-align: center;
        }

        table {
            width: 100%;
            min-width: 1400px;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 15px;
            text-align: left;
        }

        th {
            background: #343a40;
            color: white;
            position: sticky;
            top: 0;
            font-size: 15px;
        }

        tr:hover {
            background: #f0f8ff;
        }

        table#onboardedTable th, 
        table#onboardedTable td {
            text-align: center;
        }
    </style>
</head>
<body>

<!-- ✅ Sidebar -->
<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="it_asset_count.php"><i class="material-icons">arrow_right</i> IT Asset Count</a>
    <a href="it_onboarding.php"><i class="material-icons">arrow_right</i> IT Onboarding</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i> Logout</a>
</div>

<!-- ✅ Main Content -->
<div class="main-content">
    <div class="tab-header">RAK - Onboarded Employees</div>

    <div class="table-wrapper">
        <table id="onboardedTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Employee ID</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Manager</th>
                    <th>Joining Date</th>
                    <th>SNOW Ticket</th>
                    <th>PC Type</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['employee_id']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= htmlspecialchars($row['manager_name']) ?></td>
                        <td><?= htmlspecialchars($row['joining_date']) ?></td>
                        <td><?= htmlspecialchars($row['snow_ticket']) ?></td>
                        <td><?= htmlspecialchars($row['pc_type']) ?></td>
                        <td><strong style="color:green;"><?= htmlspecialchars($row['onboarding_status']) ?></strong></td>
                        <td><?= $row['created_at'] ? $row['created_at']->format('Y-m-d H:i:s') : '' ?></td>
                        <td><?= $row['updated_at'] ? $row['updated_at']->format('Y-m-d H:i:s') : '' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
