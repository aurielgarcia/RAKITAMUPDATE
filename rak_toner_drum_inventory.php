<?php
// rak_toner_drum_inventory.php
session_start();
include('db_connect.php');

// Sorting logic
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';
$orderToggle = $order === 'ASC' ? 'desc' : 'asc';

// SQL Server query
$sql = "SELECT id, printer_name, printer_location, model, color, type, stock_quantity, status FROM itequip_inventory.toner_drum_inventory ORDER BY stock_quantity $order";
$stmt = sqlsrv_query($conn, $sql);

// Check if query execution was successful
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Printer Toner & Drum Inventory | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="favicon.jpg" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .table-wrapper {
            max-height: calc(104vh - 200px); /* Adjust 200px as needed for header/footer space */
            overflow-y: auto;
            overflow-x: auto; /* Add this in case table gets wider on small screens */
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
            font-size: 15px;
        }

        table#equipmentTable {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            table-layout: auto;
            border: 1px solid #ccc; /* adds outer border */
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
        table#equipmentTable th:nth-child(6),
        table#equipmentTable td:nth-child(6),
        table#equipmentTable td:nth-child(7),
        table#equipmentTable th:nth-child(7),
        table#equipmentTable th:nth-child(8),
        table#equipmentTable td:nth-child(8) {
            text-align: center;
        }

        th:nth-child(1), td:nth-child(1) { width: 20%; }
        th:nth-child(2), td:nth-child(2) { width: 20%; }
        th:nth-child(3), td:nth-child(3) { width: 12%; }
        th:nth-child(4), td:nth-child(4) { width: 15%; }
        th:nth-child(5), td:nth-child(5) { width: 10%; }
        th:nth-child(6), td:nth-child(6) { width: 9%; }

        .color-box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #ccc;
            margin-right: 5px;
            vertical-align: middle;
        }

        .status-available {
            color: green;
            font-weight: bold;
        }

        .status-need-to-order {
            color: #FF8C00;
            font-weight: bold;
        }

        .status-out-of-stock {
            color: red;
            font-weight: bold;
        }

        .selected-row {
    background-color: #d1e7fd !important; /* Light blue highlight */
        }


    </style>
</head>
<body>

<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="it_asset_inventory.php"><i class="material-icons">arrow_right</i> IT Asset Inventory</a>
    <a href="dashboard.php"><i class="material-icons">arrow_right</i> IT Equipment Inventory</a>
    <a href="printer_inventory.php"><i class="material-icons">arrow_right</i> Printer Inventory</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="search-sort-container">
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search">
    </div>
</div>

<div class="main-content">
    <div class="tab-header">RAK - Printer Toner & Drum Inventory</div>

    <div class="table-wrapper">
        <table id="equipmentTable">
            <thead>
                <tr>
                    <th>Printer Name</th>
                    <th>Printer Location</th>
                    <th>Model Name</th>
                    <th>Color</th>
                    <th>Type</th>
                    <th>
                        <a href="?order=<?= $orderToggle ?>" style="color: white; text-decoration: none;">
                            Stock Quantity <?= $order === 'ASC' ? '▲' : '▼' ?>
                        </a>
                    </th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="inventoryTable">
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $quantity = (int)$row['stock_quantity'];
                    $status = $quantity >= 3 ? 'Available' : ($quantity >= 1 ? 'Need to Order' : 'Out Of Stock');
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['printer_name']) ?></td>
                    <td><?= htmlspecialchars($row['printer_location']) ?></td>
                    <td><?= htmlspecialchars($row['model']) ?></td>
                    <td>
                        <div class="color-box" style="background-color: <?= htmlspecialchars($row['color']) ?>;"></div>
                        <?= htmlspecialchars($row['color']) ?>
                    </td>
                    <td><?= htmlspecialchars($row['type']) ?></td>
                    <td><?= $quantity ?></td>
                    <td class="<?= strtolower(str_replace(' ', '-', 'status-' . $status)) ?>">
                        <?= $status ?>
                    </td>
                    <td>
                        <a href="edit_toner_drum.php?id=<?= $row['id'] ?>" class="icon-btn" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <a href="delete_toner_drum.php?id=<?= $row['id'] ?>" class="icon-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', function () {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#inventoryTable tr');
    
    rows.forEach(function (row) {
        let cells = row.querySelectorAll('td');
        let match = Array.from(cells).some(td => td.textContent.toLowerCase().includes(filter));
        row.style.display = match ? '' : 'none';
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            row.addEventListener('click', function () {
                // Remove highlight from all rows
                rows.forEach(r => r.classList.remove('selected-row'));

                // Highlight the clicked row
                this.classList.add('selected-row');
            });
        });
    });
</script>

</body>
</html>
