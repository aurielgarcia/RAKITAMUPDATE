<?php
include 'db_connect.php';

$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';
$toggle_order = $order === 'ASC' ? 'desc' : 'asc';

$sql = "SELECT 
            id, 
            name AS item_name, 
            model, 
            site_location, 
            storage_location, 
            stock_quantity, 
            status AS item_status, 
            remarks 
        FROM itequip_inventory.equipment 
        ORDER BY item_name ASC, site_location ASC, storage_location ASC, stock_quantity $order";

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
        .col-model    { width: 15%; }
        .col-site     { width: 10%; }
        .col-storage  { width: 15%; }
        .col-stock    { width: 8%; }
        .col-status   { width: 12%; }
        .col-remarks  { width: 15%; }
        .col-action   { width: 10%; }

        .text-center {
            text-align: center !important;
        }

        .status-available { color: #28a745; font-weight: bold; }
        .status-out { color: #dc3545; font-weight: bold; }
        .status-need-to-order { color: #FF8C00; font-weight: bold; }        
        .selected-row { background-color: #e8f4ff !important; }
        tr:hover { background-color: #f8f9fa; }

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
            min-width: 150px;
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
    <a href="rak_toner_drum_inventory.php"><i class="material-icons">arrow_right</i> Printer Toner & Drum Inventory</a>
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
                <option value="0">Item Description</option>
                <option value="1">Device Model</option>
                <option value="2">Site Location</option>
                <option value="3">Storage Location</option>
                <option value="5">Status</option>
            </select>

            <select id="valueFilter" class="filter-select">
                <option value="">Choose Value...</option>
            </select>

            <button onclick="resetFilters()" style="padding: 10px 15px; cursor: pointer; border: 1px solid #ccc; border-radius: 5px; background: #f8f9fa;">
                <i class="fas fa-sync-alt"></i> Reset
            </button>
        </div>
    </div>

    <div class="tab-header">RAK - IT Equipment Inventory</div>

    <div class="table-wrapper">
        <table id="equipmentTable">
            <thead>
                <tr>
                    <th class="col-item text-center">Item Description</th>
                    <th class="col-model text-center">Device Model</th>
                    <th class="col-site text-center">Site Location</th>
                    <th class="col-storage text-center">Storage Location</th>
                    <th class="col-stock text-center">
                        <a href="dashboard.php?order=<?= $toggle_order ?>" style="text-decoration: none; color: inherit;">
                            Stock <?= $order === 'ASC' ? '▲' : '▼' ?>
                        </a>
                    </th>
                    <th class="col-status text-center">Status</th>
                    <th class="col-remarks text-center">Remarks</th>
                    <th class="col-action text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory as $row): 
                    $siteClass = 'site-default';
                    if ($row['site_location'] === 'Al Ghail') $siteClass = 'site-ghail';
                    if ($row['site_location'] === 'Al Hamra') $siteClass = 'site-hamra';
                    $stock = (int)($row['stock_quantity'] ?? 0);
                ?>
                <tr>
                    <td style="font-weight: 500;"><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= htmlspecialchars($row['model'] ?? 'N/A') ?></td>
                    <td class="text-center">
                        <span class="site-badge <?= $siteClass ?>">
                            <?= htmlspecialchars($row['site_location'] ?: 'Not Set') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['storage_location'] ?: 'N/A') ?></td>
                    <td class="text-center"><?= $stock ?></td>
                    <td class="text-center">
                        <?php if ($stock === 0): ?>
                            <span class="status-out">Out of Stock</span>
                        <?php elseif ($stock <= 3): ?>
                            <span class="status-need-to-order">Need to Order</span>
                        <?php else: ?>
                            <span class="status-available">Available</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-left" style="font-size: 12px; color: #666;"><?= htmlspecialchars($row['remarks'] ?? '') ?></td>
                    <td class="text-center">
                        <a href="edit_equipment.php?id=<?= $row['id'] ?>" class="icon-btn" title="Edit">
                            <i class="fas fa-pen" style="color: #007bff;"></i>
                        </a>
                        <a href="delete_equipment.php?id=<?= $row['id'] ?>" class="icon-btn delete" title="Delete"
                           onclick="return confirm('Confirm deletion of this record for <?= addslashes($row['site_location']) ?> - <?= addslashes($row['storage_location']) ?>?');">
                            <i class="fas fa-trash" style="color: #dc3545; margin-left: 10px;"></i>
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
                const text = row.cells[colIndex].textContent.trim();
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
        
        const rows = document.querySelectorAll('#equipmentTable tbody tr');
        rows.forEach(row => row.style.display = '');
    }
</script>

</body>
</html>