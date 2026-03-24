<?php
session_start();
include('db_connect.php');

$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';
$orderToggle = $order === 'ASC' ? 'desc' : 'asc';

$sql = "SELECT id, printer_name, printer_location, model, site_location, storage_location, color, type, stock_quantity, status FROM itequip_inventory.toner_drum_inventory ORDER BY stock_quantity $order";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$inventory = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $inventory[] = $row;
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
            max-height: calc(100vh - 220px);
            overflow-y: auto;
            overflow-x: auto;
            border: 1px solid #ddd;
            width: 100%;
            box-sizing: border-box;
            background: #fff;
        }

        table#equipmentTable {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            table-layout: fixed;
        }

        table#equipmentTable th {
            position: sticky;
            top: 0;
            background-color: #343a40;
            color: white;
            z-index: 10;
            padding: 12px 10px;
            text-align: left;
            border: 1px solid #454d55;
        }

        table#equipmentTable td {
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .col-item     { width: 15%; }
        .col-site    { width: 8%; }
        .col-printloc     { width: 15%; }
        .col-model  { width: 10%; }
        .col-color    { width: 8%; }
        .col-type   { width: 7%; }
        .col-stock  { width: 7%; }
        .col-storloc   { width: 12%; }
        .col-status   { width: 12%; }
        .col-action   { width: 6%; }

        .text-center {
            text-align: center !important;
        }

        .status-available { color: #28a745; font-weight: bold; }
        .status-out { color: #dc3545; font-weight: bold; }
        .status-need-to-order { color: #FF8C00; font-weight: bold; }
        .selected-row { background-color: #e8f4ff !important; }
        tr:hover { background-color: #f8f9fa; }

        .color-box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #ccc;
            margin-right: 5px;
            vertical-align: middle;
        }

        .site-badge {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .site-ghail { background: #e3f2fd; color: #0d47a1; }
        .site-hamra { background: #f3e5f5; color: #4a148c; }
        .site-default { background: #e9ecef; color: #495057; }

        .filter-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .filter-select {
            padding: 9px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
            min-width: 160px;
        }
        #valueFilter {
            display: none;
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

<div class="main-content">

    <div class="search-sort-container" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <div class="filter-controls">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search..." style="width: 250px; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
            </div>

            <select id="columnFilter" class="filter-select">
                <option value="">Filter</option>
                <option value="0">Printer Name</option>
                <option value="1">Site</option>
                <option value="3">Device Model</option>
                <option value="4">Color</option>
                <option value="5">Type</option>
                <option value="8">Status</option>
            </select>

            <select id="valueFilter" class="filter-select">
                <option value="">Choose Value...</option>
            </select>

            <button onclick="resetFilters()" style="padding: 10px 15px; cursor: pointer; border: 1px solid #ccc; border-radius: 5px; background: #f8f9fa;">
                <i class="fas fa-sync-alt"></i> Reset
            </button>
        </div>
    </div>

    <div class="tab-header">RAK - Printer Toner & Drum Inventory</div>

    <div class="table-wrapper">
        <table id="equipmentTable">
            <thead>
                <tr>
                    <th class="col-item text-center">Printer Name</th>
                    <th class="col-site text-center">Site</th>
                    <th class="col-printloc text-center">Printer Location</th>
                    <th class="col-model">Device Model</th>
                    <th class="col-color text-center">Color</th>
                    <th class="col-type text-center">Type</th>
                    <th class="col-stock text-center">
                        <a href="?order=<?= $orderToggle ?>" style="color: white; text-decoration: none;">
                            Stock Quantity <?= $order === 'ASC' ? '▲' : '▼' ?>
                        </a>
                    </th>
                    <th class="col-storloc text-center">Storage Location</th>
                    <th class="col-status text-center">Status</th>
                    <th class="col-action text-center">Action</th>
                </tr>
            </thead>
            <tbody id="inventoryTable">
                <?php foreach ($inventory as $row): 
                    $siteClass = 'site-default';
                    if ($row['site_location'] === 'Al Ghail') $siteClass = 'site-ghail';
                    if ($row['site_location'] === 'Al Hamra') $siteClass = 'site-hamra';
                    
                    $stock = (int)$row['stock_quantity'];

                    $statusText = "Available";
                    if ($stock === 0) $statusText = "Out of Stock";
                    elseif ($stock <= 3) $statusText = "Need to Order";
                ?>               
                <tr>
                    <td><?= htmlspecialchars($row['printer_name']) ?></td>
                    <td class="text-center">
                        <span class="site-badge <?= $siteClass ?>">
                            <?= htmlspecialchars($row['site_location'] ?: 'Not Set') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['printer_location']) ?></td>
                    <td><?= htmlspecialchars($row['model']) ?></td>
                    <td>
                        <div class="color-box" style="background-color: <?= htmlspecialchars($row['color']) ?>;"></div>
                        <?= htmlspecialchars($row['color']) ?>
                    </td>
                    <td><?= htmlspecialchars($row['type']) ?></td>
                    <td class="text-center"><?= $stock ?></td>
                    <td><?= htmlspecialchars($row['storage_location'] ?: 'N/A') ?></td>
                    <td class="text-center">
                        <?php if ($stock === 0): ?>
                            <span class="status-out">Out of Stock</span>
                        <?php elseif ($stock <= 3): ?>
                            <span class="status-need-to-order">Need to Order</span>
                        <?php else: ?>
                            <span class="status-available">Available</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="edit_toner_drum.php?id=<?= $row['id'] ?>" class="icon-btn" title="Edit">
                            <i class="fas fa-pen" style="color: #007bff;"></i>
                        </a>
                        <a href="delete_toner_drum.php?id=<?= $row['id'] ?>" class="icon-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
                            <i class="fas fa-trash" style="color: #dc3545; margin-left: 5px;"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('equipmentTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const columnFilter = document.getElementById('columnFilter');
    const valueFilter = document.getElementById('valueFilter');
    const globalSearch = document.getElementById('searchInput');

    columnFilter.addEventListener('change', function() {
        const colIndex = this.value;
        valueFilter.innerHTML = '<option value="">Choose Value...</option>';
        
        if (colIndex === "") {
            valueFilter.style.display = 'none';
            applyFilters();
            return;
        }

        const uniqueValues = new Set();
        rows.forEach(row => {
            let text = row.cells[colIndex].textContent.trim();
            if (text) uniqueValues.add(text);
        });

        Array.from(uniqueValues).sort().forEach(val => {
            const opt = document.createElement('option');
            opt.value = val;
            opt.textContent = val;
            valueFilter.appendChild(opt);
        });

        valueFilter.style.display = 'block';
        applyFilters();
    });

    function applyFilters() {
        const globalQuery = globalSearch.value.toLowerCase();
        const colIndex = columnFilter.value;
        const selectedValue = valueFilter.value.toLowerCase();

        rows.forEach(row => {
            const rowText = row.innerText.toLowerCase();
            const matchesGlobal = rowText.includes(globalQuery);
            
            let matchesColumn = true;
            if (colIndex !== "" && selectedValue !== "") {
                const cellText = row.cells[colIndex].textContent.trim().toLowerCase();
                matchesColumn = (cellText === selectedValue);
            }

            row.style.display = (matchesGlobal && matchesColumn) ? '' : 'none';
        });
    }

    globalSearch.addEventListener('input', applyFilters);
    valueFilter.addEventListener('change', applyFilters);

    table.addEventListener('click', function (e) {
        const row = e.target.closest('tr');
        if (!row || row.parentElement.tagName === 'THEAD') return;
        rows.forEach(r => r.classList.remove('selected-row'));
        row.classList.add('selected-row');
    });
});

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('columnFilter').value = '';
    const valueFilter = document.getElementById('valueFilter');
    valueFilter.innerHTML = '<option value="">Choose Value...</option>';
    valueFilter.style.display = 'none';
    
    const rows = document.querySelectorAll('#inventoryTable tr');
    rows.forEach(row => row.style.display = '');
}
</script>

</body>
</html>