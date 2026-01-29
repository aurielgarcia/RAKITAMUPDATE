<?php
include 'db_connect.php';

$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';
$toggle_order = $order === 'ASC' ? 'desc' : 'asc';

$sql = "SELECT 
            id, 
            name AS item_name, 
            model, 
            storage_location, 
            stock_quantity, 
            status AS item_status, 
            remarks 
        FROM itequip_inventory.equipment 
        ORDER BY stock_quantity $order";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Equipment Inventory | VERTIV GULF LLC ITMS</title>
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
        table#equipmentTable td:nth-child(4),
        table#equipmentTable th:nth-child(5),
        table#equipmentTable td:nth-child(5),
        table#equipmentTable th:nth-child(6),
        table#equipmentTable th:nth-child(7),
        table#equipmentTable td:nth-child(7) {
            text-align: center;
        }

        th:nth-child(1), td:nth-child(1) { width: 18%; }
        th:nth-child(2), td:nth-child(2) { width: 15%; }
        th:nth-child(3), td:nth-child(3) { width: 20%; }
        th:nth-child(4), td:nth-child(4) { width: 8%; }
        th:nth-child(5), td:nth-child(5) { width: 10%; }
        th:nth-child(6), td:nth-child(6) { width: 15%; }
        th:nth-child(7), td:nth-child(7) { width: 10%; }

        .status-available {
            color: green;
            font-weight: bold;
        }

        .status-out {
            color: red;
            font-weight: bold;
        }

        .selected-row {
    background-color: #d1e7fd !important; /* Light blue highlight */
        }

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="it_asset_inventory.php"><i class="material-icons">arrow_right</i> IT Asset Inventory</a>
    <a href="rak_toner_drum_inventory.php"><i class="material-icons">arrow_right</i> Printer Toner & Drum Inventory</a>
    <a href="printer_inventory.php"><i class="material-icons">arrow_right</i> Printer Inventory</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="search-sort-container">
    <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search">
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div style="text-align: right; margin: 10px 20px;">
    <a href="export_itequipment.php?order=<?= strtolower($order) ?>" class="icon-btn" title="Export to Excel" style="color: green; font-size: 20px;">
        <i class="fas fa-file-excel"></i> Export
    </a>
</div>
    <div class="tab-header">RAK - IT Equipment Inventory</div>

    <div class="table-wrapper">
        <table id="equipmentTable">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Device Model</th>
                    <th>Storage Location</th>
                    <th>
                        <a href="dashboard.php?order=<?= $toggle_order ?>&t=<?= time() ?>" style="text-decoration: none; color: inherit;">
                            Stock Quantity <?= $order === 'ASC' ? '▲' : '▼' ?>
                        </a>
                    </th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= htmlspecialchars($row['model'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['storage_location']) ?></td>
                    <td><?= isset($row['stock_quantity']) ? (int)$row['stock_quantity'] : 0 ?></td>
                    <td>
                        <?php if ((int)$row['stock_quantity'] > 0): ?>
                            <span class="status-available">Available</span>
                        <?php else: ?>
                            <span class="status-out">Out of Stock</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['remarks'] ?? '') ?></td>
                    <td>
                        <a href="edit_equipment.php?id=<?= $row['id'] ?>" class="icon-btn" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <a href="delete_equipment.php?id=<?= $row['id'] ?>" class="icon-btn delete" title="Delete"
                           onclick="return confirm('Are you sure you want to delete this item?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#equipmentTable tbody tr');

        rows.forEach(row => {
            const itemDesc = row.children[0].textContent.toLowerCase();
            const model = row.children[1].textContent.toLowerCase();
            const location = row.children[2].textContent.toLowerCase();
            const status = row.children[4].textContent.toLowerCase();

            if (
                itemDesc.includes(filter) ||
                model.includes(filter) ||
                location.includes(filter) ||
                status.includes(filter)
            ) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
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
