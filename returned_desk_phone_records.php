<?php
include('db_connect.php');

// Fetch returned desk phone records
$sql = "SELECT requested_by, equipment_name, model, return_date 
        FROM itequip_inventory.checkouts 
        WHERE equipment_name = 'Desk Phone' AND return_status = 'returned' 
        ORDER BY return_date DESC";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Returned Desk Phone Records | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .table-wrapper {
            max-height: calc(100.9vh - 200px);
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
            border: 1px solid #ccc; /* adds grid lines */
        }

        table#equipmentTable th {
            text-align: center;
        }

        table#equipmentTable td:nth-child(1) {
            text-align: left;
        }  

         table#equipmentTable td:nth-child(4) {
            text-align: right;
        }  

        table#equipmentTable td:nth-child(2),
        table#equipmentTable td:nth-child(3) {
            text-align: center;
        }  

        th:nth-child(1), td:nth-child(1) { width: 25%; }
        th:nth-child(2), td:nth-child(2) { width: 20%; }
        th:nth-child(3), td:nth-child(3) { width: 20%; }
        th:nth-child(4), td:nth-child(4) { width: 20%; }
    </style>
</head>
<body>
<div class="main-content">

<!-- Sidebar -->
<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="users_view.php"><i class="material-icons">arrow_right</i>Assigned IT Assets and Equipment</a>
    <a href="returned_it_asset_records.php"><i class="material-icons">arrow_right</i>Returned IT Asset</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="container">
    <div class="tab-header">Returned Desk Phone Records</div>

    <div class="table-wrapper">
        <table id="equipmentTable">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Item Description</th>
                    <th>Device Model</th>
                    <th>Return Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['requested_by']) ?></td>
                        <td><?= htmlspecialchars($row['equipment_name']) ?></td>
                        <td><?= htmlspecialchars($row['model']) ?></td>
                        <td>
                            <?= $row['return_date'] instanceof DateTime 
                                ? $row['return_date']->format('M-d-Y') 
                                : '' ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
