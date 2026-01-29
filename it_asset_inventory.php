<?php
include 'db_connect.php';

// === Start of update block ===
$sqlSelect = "SELECT id, item_description, serial_number FROM itequip_inventory.dbo.it_asset_inventory";
$stmtUpdate = sqlsrv_query($conn, $sqlSelect);

if ($stmtUpdate === false) {
    die(print_r(sqlsrv_errors(), true));
}

while ($row = sqlsrv_fetch_array($stmtUpdate, SQLSRV_FETCH_ASSOC)) {
    $id = $row['id'];
    $itemDescription = trim($row['item_description']);
    $serialNumber = trim($row['serial_number']);
    $newDeviceName = '';

    if ($itemDescription === 'All-In-One' || $itemDescription === 'Desktop') {
    $newDeviceName = 'AE-D-' . $serialNumber;
} elseif ($itemDescription === 'Laptop') {
    $newDeviceName = 'AE-L-' . $serialNumber;
} else {
    $newDeviceName = null;
}

    if ($newDeviceName !== null) {
        $sqlUpdate = "UPDATE itequip_inventory.dbo.it_asset_inventory SET device_name = ? WHERE id = ?";
        $paramsUpdate = [$newDeviceName, $id];
        $updateStmt = sqlsrv_query($conn, $sqlUpdate, $paramsUpdate);

        if ($updateStmt === false) {
            echo "Error updating ID $id: ";
            print_r(sqlsrv_errors());
        }
    }
}
// === End of update block ===


// Allowed columns to sort by (only these 5)
$allowedSortColumns = ['item_description', 'device_model', 'device_type', 'deployed_date', 'install_status', 'assigned_to'];

// Default sort column and order
$sortColumn = 'deployed_date';
$sortOrder = 'DESC';

// Check if sort parameters exist and are valid
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowedSortColumns)) {
    $sortColumn = $_GET['sort'];
}

if (isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC'])) {
    $sortOrder = strtoupper($_GET['order']);
}

// Build SQL with ORDER BY
$sql = "SELECT 
            id, 
            item_description, 
            device_model, 
            serial_number, 
            device_processor, 
            device_storage, 
            device_ram, 
            device_gpu, 
            device_type,
            device_name, 
            install_status, 
            deployed_by, 
            deployed_date,
            purchased_date, 
            assigned_to, 
            service_now_request_ticket,
            device_storage_location,
            remarks
        FROM itequip_inventory.dbo.it_asset_inventory
        ORDER BY $sortColumn $sortOrder";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Function to get the opposite order for toggle
function toggleOrder($currentOrder) {
    return $currentOrder === 'ASC' ? 'DESC' : 'ASC';
}

// Helper function to display arrow icons for sorting
function sortArrow($column, $currentSortColumn, $currentSortOrder) {
    if ($column === $currentSortColumn) {
        return $currentSortOrder === 'ASC' ? ' &uarr;' : ' &darr;';
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Asset Inventory | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>

        html, body {
            margin: 0;
            padding: 0;
            overflow-y: auto; /* allow scroll only if needed */
        }

        .table-wrapper {
            height: calc(100.9vh - 200px); /* use height instead of max-height */
            overflow-y: auto; /* only this scrolls */
            overflow-x: auto;
            border: 2px solid #ddd;
            box-sizing: border-box;
            width: 100%;
        }

        table {
            width: 100%;        /* Expand with screen */
            min-width: 1400px;  /* Prevent shrinking too small */
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            padding: 11px 13px;
            border: 1px solid #ccc;
            text-align: left;
            white-space: nowrap;
        }

        th {
            background-color: #343a40;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1;
            cursor: default;
            user-select: none;
            font-size: 15px;
        }

        /* Make only sortable headers have pointer cursor */
        th.sortable {
            cursor: pointer;
        }

        th a {
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        th a:hover {
            text-decoration: underline;
        }

        /* Arrow spacing */
        th a span.arrow {
            margin-left: 5px;
        }

        .status-installed {
            color: green;
            font-weight: bold;
        }

        .status-uninstalled {
            color: red;
            font-weight: bold;
        }

        /* Modal styles */

        h2 {
           text-align: center;
        }

        .button-wrapper {
    text-align: center;
}

.modal-content button {
    background: #2980b9; 
    color: white; 
    padding: 8px 16px;
    border: none; 
    border-radius: 5px; 
    cursor: pointer;
    margin-right: 20px;
}
        
.modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0,0,0,0.5); 
}

.modal-content {
    background-color: #fff;
    margin: 10% auto; 
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 700px;
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0,0,0,0.3);
    position: relative;
}

.modal .close {
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.modal .close:hover,
.modal .close:focus {
    color: black;
}

form {
    background: none !important;
    box-shadow: none !important;
    margin: 0;
    padding: 0;
    border: none;
}

.edit-btn {
             cursor: pointer;
             margin-right: 5px;
        }

.del-btn {
             cursor: pointer;
        }

        table#equipmentTable th:nth-child(1),
        table#equipmentTable th:nth-child(2),
        table#equipmentTable td:nth-child(2),
        table#equipmentTable th:nth-child(3),
        table#equipmentTable th:nth-child(4),
        table#equipmentTable td:nth-child(4),
        table#equipmentTable th:nth-child(5),
        table#equipmentTable td:nth-child(5),
        table#equipmentTable th:nth-child(6),
        table#equipmentTable th:nth-child(7),
        table#equipmentTable td:nth-child(7),
        table#equipmentTable th:nth-child(8),
        table#equipmentTable th:nth-child(9),
        table#equipmentTable th:nth-child(10),
        table#equipmentTable td:nth-child(10),
        table#equipmentTable th:nth-child(11) {
            text-align: center;
        }

        table#equipmentTable td:nth-child(3),
        table#equipmentTable td:nth-child(6),
        table#equipmentTable td:nth-child(8),
        table#equipmentTable td:nth-child(9),
        table#equipmentTable td:nth-child(11) {
            text-align: left;
        }

        table#equipmentTable td:nth-child(1) {
            text-align: right;
        }


        .selected-row {
    background-color: #d1e7fd !important; /* Light blue highlight */
        }

        .device-link:link {
            color: blue;
            text-decoration: none;
        }

        .device-link:link,
        .device-link:visited {
            color: blue;        /* Always blue */
            text-decoration: none; /* No underline by default */
        }

        .device-link:hover {
            color: darkblue;     /* Optional: darker blue on hover */
            text-decoration: underline; /* Underline on hover */
        }
        
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="dashboard.php"><i class="material-icons">arrow_right</i> IT Equipment Inventory</a>
    <a href="rak_toner_drum_inventory.php"><i class="material-icons">arrow_right</i> Printer Toner & Drum Inventory</a>
    <a href="printer_inventory.php"><i class="material-icons">arrow_right</i> Printer Inventory</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<!-- Search -->
<div class="search-sort-container">
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search">
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div style="text-align: right; margin: 10px 20px;">
    <a href="export_it_asset.php" title="Export to CSV" sclass="icon-btn" style="color: blue; font-size: 20px;">
    <i class="fa fa-file-csv"></i> Export
</a>
    </a>
    </div>

    <div class="tab-header">RAK - IT Asset Inventory</div>

    <div class="table-wrapper">
        <table id="equipmentTable">
            <thead>
                <tr>
                    <!-- Sortable headers -->
                     <th class="sortable">
                        <a href="?sort=deployed_date&order=<?= toggleOrder($sortOrder) ?>&<?= http_build_query(array_diff_key($_GET, ['sort'=>'', 'order'=> ''])) ?>">
                            Deployed Date<?= sortArrow('deployed_date', $sortColumn, $sortOrder) ?>
                        </a>
                    </th>
                    <th class="sortable">
                        <a href="?sort=item_description&order=<?= toggleOrder($sortOrder) ?>&<?= http_build_query(array_diff_key($_GET, ['sort'=>'', 'order'=> ''])) ?>">
                            Item Description<?= sortArrow('item_description', $sortColumn, $sortOrder) ?>
                        </a>
                    </th>
                    <th class="sortable">
                        <a href="?sort=device_model&order=<?= toggleOrder($sortOrder) ?>&<?= http_build_query(array_diff_key($_GET, ['sort'=>'', 'order'=> ''])) ?>">
                            Device Model<?= sortArrow('device_model', $sortColumn, $sortOrder) ?>
                        </a>
                        <!-- Sortable header -->
                    <th class="sortable">
                        <a href="?sort=device_type&order=<?= toggleOrder($sortOrder) ?>&<?= http_build_query(array_diff_key($_GET, ['sort'=>'', 'order'=> ''])) ?>">
                            Device Type<?= sortArrow('device_type', $sortColumn, $sortOrder) ?>
                        </a>
                    <th>Device Name</th> <!-- New column header -->
                    </th>
                     <!-- Sortable header -->
                    <th class="sortable">
                        <a href="?sort=assigned_to&order=<?= toggleOrder($sortOrder) ?>&<?= http_build_query(array_diff_key($_GET, ['sort'=>'', 'order'=> ''])) ?>">
                            Assigned To<?= sortArrow('assigned_to', $sortColumn, $sortOrder) ?>
                        </a>
                    </th>
                    <!-- Sortable header -->
                    <th class="sortable">
                        <a href="?sort=install_status&order=<?= toggleOrder($sortOrder) ?>&<?= http_build_query(array_diff_key($_GET, ['sort'=>'', 'order'=> ''])) ?>">
                            Install Status<?= sortArrow('install_status', $sortColumn, $sortOrder) ?>
                        </a>
                    </th>
                    <!-- Non-sortable headers -->
                    <th>ServiceNow Ticket</th>          
                    <th>Deployed By</th>               
                    <th>Device Storage Location</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                <tr>
                    <td><?= isset($row['deployed_date']) ? $row['deployed_date']->format('M-d-Y') : '' ?></td>
                    <td><?= htmlspecialchars($row['item_description']) ?></td>
                    <td><?= htmlspecialchars($row['device_model']) ?></td>
                    <td><?= htmlspecialchars($row['device_type']) ?></td>
                    <td>
                        <?php
                            $deviceName = $row['device_name'];
                            $serialNumber = $row['serial_number'];
                            $itemDescription = trim($row['item_description']);

                            // Build the display name
                            if ($itemDescription === 'All In One PC' && empty($deviceName)) {
                                $displayName = 'AE-D-' . htmlspecialchars($serialNumber);
                            } elseif ($itemDescription === 'Laptop') {
                                $displayName = 'AE-L-' . htmlspecialchars($serialNumber);
                            } else {
                                $displayName = htmlspecialchars($deviceName);
                            }

                            // Make it clickable with a class
                                echo '<a href="it_asset_specs.php?id=' . $row['id'] . '" class="device-link">' . $displayName . '</a>';
                            ?>
                    </td>
                    <td><?= htmlspecialchars($row['assigned_to']) ?></td>
                    <td><?= htmlspecialchars($row['install_status']) ?></td>
                    <td><?= htmlspecialchars($row['service_now_request_ticket']) ?></td>
                    <td><?= htmlspecialchars($row['deployed_by']) ?></td>
                    <td><?= htmlspecialchars($row['device_storage_location']) ?></td>
                    <td><?= htmlspecialchars($row['remarks']) ?></td>
                    <td>
                        <i class="fas fa-pen edit-btn" data-id="<?= $row['id'] ?>"></i>
                        <i class="fas fa-trash-alt del-btn" onclick="openDeleteModal(<?= $row['id'] ?>)"></i>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Edit Modal -->
<div id="editModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div class="modal-content" style="background:white; padding:20px; max-width:600px; margin:5% auto; border-radius:8px; position:relative;">
        <span onclick="closeModal()" style="position:absolute; top:10px; right:15px; font-size:20px; cursor:pointer;">&times;</span>
        <h2>Edit IT Asset</h2>
        <form action="update_it_asset.php" method="POST">
            <input type="hidden" id="edit-id" name="id">

            <label>Deployed Date:</label>
            <input type="date" id="edit-deployed_date" name="deployed_date" style="text-align:center;"><br>
            
            <label>Item Description:</label>
            <input type="text" id="edit-item_description" name="item_description" required style="text-align:center;"><br>

            <label>Device Model:</label>
            <input type="text" id="edit-device_model" name="device_model" style="text-align:center;"><br>

             <label>Device Type:</label>
            <input type="text" id="edit-device_type" name="device_type" style="text-align:center;"><br>

            <label>Assigned To:</label>
            <input type="text" id="edit-assigned_to" name="assigned_to" style="text-align:center;"><br>

            <label>Install Status:</label>
            <select id="edit-install_status" name="install_status" style="text-align:center;">
            <option value="In Stock - New">In Stock - New</option>
            <option value="In Stock - Spare">In Stock - Spare</option>
            <option value="Installed">Installed</option>
            <option value="Retired">Retired</option>
            </select>

            <label>ServiceNow Request Ticket:</label>
            <input type="text" id="edit-service_now_request_ticket" name="service_now_request_ticket" style="text-align:center;"><br>

            <label>Device Processor:</label>
            <input type="text" id="edit-device_processor" name="device_processor" style="text-align:center;"><br>

            <label>Device Storage:</label>
            <input type="text" id="edit-device_storage" name="device_storage" style="text-align:center;"><br>

            <label>Device RAM:</label>
            <input type="text" id="edit-device_ram" name="device_ram" style="text-align:center;"><br>

            <label>Device GPU:</label>
            <input type="text" id="edit-device_gpu" name="device_gpu" style="text-align:center;"><br>

            <label>Serial Number:</label>
            <input type="text" id="edit-serial_number" name="serial_number" style="text-align:center;"><br>

            <label>Purchased Date:</label>
            <input type="date" id="edit-purchased_date" name="purchased_date" style="text-align:center;"><br>

            <label>Deployed By:</label>
            <input type="text" id="edit-deployed_by" name="deployed_by" style="text-align:center;"><br>
          
            <label>Device Storage Location:</label>
            <select id="edit-device_storage_location" name="device_storage_location" style="text-align:center;">
            <option value="I.T. Department Main Building">I.T. Department Main Building</option>
            <option value="Stock Room Main Building">Stock Room Main Building</option>
            <option value="Released to the User">Released to the User</option>
            <option value="Out">Out</option>
            <option value="Out For Repair">Out For Repair</option>s
            </select>

            <label>Remarks:</label>
            <input type="text" id="edit-remarks" name="remarks" style="text-align:center;"><br>
            
            <div class="button-wrapper">
            <button type="submit">Update</button>
            </div>
            
        </form>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
    <div class="modal-content" style="background:white; padding:20px; max-width:400px; margin:10% auto; border-radius:8px; text-align:center;">
        <p>Are you sure you want to delete this record?</p>
        <button id="confirmDeleteBtn" style="background:red; color:white; padding:8px 16px; margin-right:10px;">Yes, Delete</button>
        <button onclick="closeDeleteModal()" style="padding:8px 16px;">Cancel</button>
    </div>
</div>



<script>
function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

function formatDateForInput(dateString) {
    if (!dateString) return '';

    const date = new Date(dateString);
    if (isNaN(date)) return ''; // Invalid date

    return date.toISOString().split('T')[0]; // Format: YYYY-MM-DD
}

document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.getAttribute('data-id');

        fetch('get_it_asset.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-deployed_date').value = formatDateForInput(data.deployed_date);
                document.getElementById('edit-item_description').value = data.item_description;
                document.getElementById('edit-device_model').value = data.device_model;
                document.getElementById('edit-device_type').value = data.device_type;
                document.getElementById('edit-assigned_to').value = data.assigned_to;
                document.getElementById('edit-install_status').value = data.install_status;
                document.getElementById('edit-service_now_request_ticket').value = data.service_now_request_ticket;
                document.getElementById('edit-device_processor').value = data.device_processor;
                document.getElementById('edit-device_storage').value = data.device_storage;
                document.getElementById('edit-device_ram').value = data.device_ram;
                document.getElementById('edit-device_gpu').value = data.device_gpu;
                document.getElementById('edit-serial_number').value = data.serial_number;
                document.getElementById('edit-purchased_date').value = formatDateForInput(data.purchased_date);
                document.getElementById('edit-deployed_by').value = data.deployed_by;
                document.getElementById('edit-device_storage_location').value = data.device_storage_location;
                document.getElementById('edit-remarks').value = data.remarks;

                document.getElementById('editModal').style.display = 'block';
            })
            .catch(error => {
                alert('Failed to fetch item details.');
                console.error(error);
            });
    });
});
</script>


<script>
let deleteId = null;

function openDeleteModal(id) {
    deleteId = id;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    deleteId = null;
    document.getElementById('deleteModal').style.display = 'none';
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (!deleteId) return;

    fetch('delete_it_asset.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + deleteId
    })
    .then(response => response.text())
    .then(data => {
        alert('Record deleted.');
        closeDeleteModal();
        location.reload(); // or remove row dynamically
    })
    .catch(err => {
        alert('Delete failed.');
        console.error(err);
    });
});
</script>

<script>
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#equipmentTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
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