<?php
include 'db_connect.php';


// Delete printer if delete_id is set
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $deleteSql = "DELETE FROM dbo.printer_inventory WHERE id = ?";
    $params = [$deleteId];
    $stmtDelete = sqlsrv_query($conn, $deleteSql, $params);

    if ($stmtDelete === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Redirect back to avoid resubmission
    header("Location: printer_inventory.php");
    exit;
}

// Allowed columns to sort by
$allowedSortColumns = ['printer_description', 'printer_model', 'printer_type', 'printer_location', 'purchased_date'];

// Default sort
$sortColumn = 'printer_description';
$sortOrder = 'ASC';

// Handle request
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowedSortColumns)) {
    $sortColumn = $_GET['sort'];
}
if (isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC'])) {
    $sortOrder = strtoupper($_GET['order']);
}

// If sorting by description, also sort model the same way
if ($sortColumn === 'printer_description') {
    $orderBy = "printer_description $sortOrder, printer_model $sortOrder";
} else {
    $orderBy = "$sortColumn $sortOrder";
}

// Build query
$sql = "SELECT 
            id,
            printer_description,
            printer_model,
            printer_type,
            printer_serialnumber,
            printer_location,
            purchased_date,
            printer_ip_address,
            printer_subnet_mask,
            printer_gateway,
            printer_dns1,
            printer_dns2,
            printer_switch,
            printer_port,
            printer_web_portal_admin_credentials
        FROM dbo.printer_inventory
        ORDER BY $orderBy";


$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

function toggleOrder($currentOrder) {
    return $currentOrder === 'ASC' ? 'DESC' : 'ASC';
}

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
    <title>Printer Inventory | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<style>
            html, body {
            margin: 0;
            padding: 0;
            overflow-y: auto; /* allow scroll only if needed */
        }

        .table-wrapper {
            height: calc(100vh - 200px); /* use height instead of max-height */
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
            padding: 15px;
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

        table#printerTable th:nth-child(1),
        table#printerTable td:nth-child(1),
        table#printerTable th:nth-child(2),
        table#printerTable th:nth-child(3),
        table#printerTable td:nth-child(3),
        table#printerTable th:nth-child(4),
        table#printerTable td:nth-child(4),
        table#printerTable th:nth-child(5),
        table#printerTable th:nth-child(6),
        table#printerTable th:nth-child(7) {
            text-align: center;
        }

        table#printerTable td:nth-child(2),
        table#printerTable td:nth-child(5) {
            text-align: left;
        }

        table#printerTable td:nth-child(6) {
            text-align: right;
        }


        .selected-row {
    background-color: #d1e7fd !important; /* Light blue highlight */
        }

        /* Make all <i> icons show pointer on hover */
        i {
            cursor: pointer;
        }

        /* Optional: add hover color change for better UX */
        i:hover {
            color: #007BFF; /* Change to any color you like */
        }

        /* Target the action icons by class */
        .edit-printer-btn,
        .del-btn,
        .fa-print {
            font-size: 15px;      /* make icons bigger */
            margin-right: 15px;   /* space between icons */
            cursor: pointer;      /* show pointer on hover */
        }

        /* Remove margin for the last icon */
        .fa-print:last-child {
            margin-right: 0;
        }

        /* Optional: hover effect for all icons */
        .edit-printer-btn:hover,
        .del-btn:hover,
        .fa-print:hover {
            color: #007BFF;       /* change color on hover */
            transform: scale(1.2); /* slightly enlarge on hover */
            transition: 0.2s;
        }

        .serial-link:link {
            color: blue;
            text-decoration: none;
        }

        .serial-link:link,
        .serial-link:visited {
            color: blue;        /* Always blue */
            text-decoration: none; /* No underline by default */
        }

        .serial-link:hover {
            color: darkblue;     /* Optional: darker blue on hover */
            text-decoration: underline; /* Underline on hover */
        }

        

        table#printerTable tbody tr:hover {
            background-color: #e9ecef;
        }

    </style>
<body>

<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="it_asset_inventory.php"><i class="material-icons">arrow_right</i> IT Asset Inventory</a>
    <a href="dashboard.php"><i class="material-icons">arrow_right</i> IT Equipment Inventory</a>
    <a href="rak_toner_drum_inventory.php"><i class="material-icons">arrow_right</i> Printer Toner & Drum Inventory</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="search-sort-container">
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search">
    </div>
</div>

<div class="main-content">
    <div style="text-align: right; margin: 10px 20px;">
    <button id="addPrinterBtn" style="background:#2980b9;color:white;padding:8px 16px;border:none;border-radius:5px;cursor:pointer;">
        <i class="fas fa-plus"></i> Add Printer
    </button>
</div>

    

    <div class="tab-header">RAK - Printer Inventory</div>

    <div class="table-wrapper">
        <table id="printerTable">
            <thead>
                <tr>
                    <th class="sortable">
                        <a href="?sort=printer_description&order=<?= toggleOrder($sortOrder) ?>">
                            Printer Description<?= sortArrow('printer_description', $sortColumn, $sortOrder) ?>
                        </a>
                    </th>
                    <th class="sortable">
                        <a href="?sort=printer_model&order=<?= toggleOrder($sortOrder) ?>">
                            Printer Model<?= sortArrow('printer_model', $sortColumn, $sortOrder) ?>
                        </a>
                    </th>
                    <th class="sortable">
                        <a href="?sort=printer_type&order=<?= toggleOrder($sortOrder) ?>">
                            Printer Type<?= sortArrow('printer_type', $sortColumn, $sortOrder) ?>
                        </a>

                    <th>Serial Number</th>

                        <th class="sortable">
                        <a href="?sort=printer_location&order=<?= toggleOrder($sortOrder) ?>">
                            Printer Location<?= sortArrow('printer_location', $sortColumn, $sortOrder) ?>
                        </a>
                    </th>
                    <th class="sortable">
                        <a href="?sort=purchased_date&order=<?= toggleOrder($sortOrder) ?>">
                            Purchased Date<?= sortArrow('purchased_date', $sortColumn, $sortOrder) ?>
                        </a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
<?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
<tr>
    <!-- Printer Description -->
    <td><?= htmlspecialchars($row['printer_description']) ?></td>

    <!-- Printer Model -->
    <td><?= htmlspecialchars($row['printer_model']) ?></td>

    <!-- Printer Type -->
    <td><?= htmlspecialchars($row['printer_type']) ?></td>

    <!-- Serial Number: clickable only if Office Printer -->
    <td>
        <?php if ($row['printer_description'] === 'Office Printer'): ?>
            <a href="printer_specs.php?id=<?= $row['id'] ?>" class="serial-link">
                <?= htmlspecialchars($row['printer_serialnumber']) ?>
            </a>
        <?php else: ?>
            <?= htmlspecialchars($row['printer_serialnumber']) ?>
        <?php endif; ?>
    </td>

    <!-- Printer Location -->
    <td><?= htmlspecialchars($row['printer_location']) ?></td>

    <!-- Purchased Date -->
    <td><?= $row['purchased_date'] ? $row['purchased_date']->format('M-d-Y') : 'N/A' ?></td>

    <!-- Actions -->
    <td>
            <!-- Edit icon -->
            <i class="fas fa-pen edit-printer-btn" data-id="<?= $row['id'] ?>" title="Edit"></i>


            <!-- Delete icon -->
            <i class="fas fa-trash-alt del-btn" 
               onclick="confirmDelete(<?= $row['id'] ?>)" 
               title="Delete"></i>

            <!-- Print icon -->
            <i class="fas fa-print" 
               onclick="window.open('print_printer.php?id=<?= $row['id'] ?>', 'Print', 'width=800,height=400,top=300,left=300')" 
               title="Print"></i>
        </td>
</tr>
<?php endwhile; ?>
</tbody>


        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addPrinterModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div class="modal-content" style="background:white; padding:20px; max-width:600px; margin:5% auto; border-radius:8px; position:relative;">
        <span class="close" id="closeAddModal">&times;</span>
        <h2>Add Printer</h2>
        <form action="add_printer.php" method="post">
            <div style="margin-bottom:10px;">
                <label for="printer_description">Printer Description:</label><br>
                <select name="printer_description" id="printer_description">
                    <option value="" required style="text-align: center;">-- Select --</option>
                    <option value="Office Printer" required style="text-align: center;" >Office Printer</option>
                    <option value="Label Printer" required style="text-align: center;">Label Printer</option>
                </select>
            </div>
            <div style="margin-bottom:10px;">
                <label for="printer_model">Printer Model:</label><br>
                <input type="text" id="printer_model" name="printer_model" required style="text-align:center;"><br>
            </div>
            <div style="margin-bottom:10px;">
                <label for="printer_type">Printer Type:</label><br>
                <select name="printer_type" id="printer_type">
                <option value="" required style="text-align: center;">-- Select Type --</option>
                    <option value="Black and White" required style="text-align: center;" >Black and White</option>
                    <option value="Color" required style="text-align: center;">Color</option>
                    <option value="Label" required style="text-align: center;">Label</option>
                </select>
            </div>
            <div style="margin-bottom:10px;">
                <label for="printer_serialnumber">Serial Number:</label><br>
                <input type="text" id="printer_serialnumber" name="printer_serialnumber" required style="text-align:center;"><br>
            </div>
            <div style="margin-bottom:10px;">
                <label for="printer_location">Location:</label><br>
                <input type="text" id="printer_location" name="printer_location" required style="text-align:center;"><br>
            </div>
            <div style="margin-bottom:10px;">
                <div style="margin-bottom:10px; display:flex; align-items:center; gap:10px;">
                <label for="purchased_date">Purchased Date:</label>
                <input type="date" id="purchased_date" name="purchased_date" style="text-align:center;">
                <label style="display:flex; align-items:center; gap:5px;">
                    <input type="checkbox" id="purchased_na"> N/A
                </label>
            </div>
            </div>

            <!-- New Fields -->
            <div style="margin-bottom:10px;"><label for="printer_ip_address">IP Address:</label><br>
                <input type="text" id="printer_ip_address" name="printer_ip_address" style="text-align:center;"></div>

            <div style="margin-bottom:10px;"><label for="printer_subnet_mask">Subnet Mask:</label><br>
                <input type="text" id="printer_subnet_mask" name="printer_subnet_mask" style="text-align:center;"></div>

            <div style="margin-bottom:10px;"><label for="printer_gateway">Gateway:</label><br>
                <input type="text" id="printer_gateway" name="printer_gateway" style="text-align:center;"></div>

            <div style="margin-bottom:10px;"><label for="printer_dns1">DNS 1:</label><br>
                <input type="text" id="printer_dns1" name="printer_dns1" style="text-align:center;"></div>

            <div style="margin-bottom:10px;"><label for="printer_dns2">DNS 2:</label><br>
                <input type="text" id="printer_dns2" name="printer_dns2" style="text-align:center;"></div>

            <div style="margin-bottom:10px;"><label for="printer_switch">Switch:</label><br>
                <input type="text" id="printer_switch" name="printer_switch" style="text-align:center;"></div>

            <div style="margin-bottom:10px;"><label for="printer_port">Port:</label><br>
                <input type="text" id="printer_port" name="printer_port" style="text-align:center;"></div>

            <div style="margin-bottom:10px;"><label for="printer_web_portal_admin_credentials">Web Portal Admin Credentials:</label><br>
                <input type="text" id="printer_web_portal_admin_credentials" name="printer_web_portal_admin_credentials" style="text-align:center;"></div>

            <div class="button-wrapper" style="text-align:center; margin-top:15px;">
                <button type="submit">Save Printer</button>
            </div>
        </form>
    </div>
</div>


<!-- Edit Printer Modal -->
<div id="editPrinterModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
  <div class="modal-content" style="background:white; padding:20px; max-width:600px; margin:5% auto; border-radius:8px; position:relative;">
    <span onclick="closePrinterModal()" style="position:absolute; top:10px; right:15px; font-size:20px; cursor:pointer;">&times;</span>
    <h2>Edit Printer</h2>
    <form action="edit_printer.php" method="POST">
      <input type="hidden" id="edit-printer-id" name="id">

      <label>Printer Description:</label>
      <input type="text" id="edit-printer_description" name="printer_description" required style="text-align:center;"><br>

      <label>Printer Model:</label>
      <input type="text" id="edit-printer_model" name="printer_model" style="text-align:center;"><br>

      <label>Printer Type:</label>
      <select name="printer_type" id="edit-printer_type" required>
           <option value="" required style="text-align: center;">-- Select Type --</option>
                <option value="Black and White" required style="text-align: center;" >Black and White</option>
                <option value="Color" required style="text-align: center;">Color</option>
                <option value="Label" required style="text-align: center;">Label</option>
        </select>

      <label>Printer Serial Number:</label>
      <input type="text" id="edit-printer_serialnumber" name="printer_serialnumber" style="text-align:center;"><br>

      <label>Printer Location:</label>
      <input type="text" id="edit-printer_location" name="printer_location" style="text-align:center;"><br>

      <label>Purchased Date:</label>
      <input type="date" id="edit-printer_purchased_date" name="purchased_date" style="text-align:center;"><br>

      <!-- New Fields -->
      <label>IP Address:</label>
      <input type="text" id="edit-printer_ip_address" name="printer_ip_address" style="text-align:center;"><br>

      <label>Subnet Mask:</label>
      <input type="text" id="edit-printer_subnet_mask" name="printer_subnet_mask" style="text-align:center;"><br>

      <label>Gateway:</label>
      <input type="text" id="edit-printer_gateway" name="printer_gateway" style="text-align:center;"><br>

      <label>DNS 1:</label>
      <input type="text" id="edit-printer_dns1" name="printer_dns1" style="text-align:center;"><br>

      <label>DNS 2:</label>
      <input type="text" id="edit-printer_dns2" name="printer_dns2" style="text-align:center;"><br>

      <label>Switch:</label>
      <input type="text" id="edit-printer_switch" name="printer_switch" style="text-align:center;"><br>

      <label>Port:</label>
      <input type="text" id="edit-printer_port" name="printer_port" style="text-align:center;"><br>

      <label>Web Portal Admin Credentials:</label>
      <input type="text" id="edit-printer_web_portal_admin_credentials" name="printer_web_portal_admin_credentials" style="text-align:center;"><br>

      <div class="button-wrapper">
        <button type="submit">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
    const addBtn = document.getElementById('addPrinterBtn');
    const addModal = document.getElementById('addPrinterModal');
    const closeAdd = document.getElementById('closeAddModal');
    
    addBtn.onclick = () => addModal.style.display = 'block';
    closeAdd.onclick = () => addModal.style.display = 'none';
    
    window.onclick = function(event) {
        if (event.target == addModal) {
            addModal.style.display = "none";
        }
    }

    // ---------- Purchased Date N/A logic ----------
    const dateInput = document.getElementById('purchased_date');
    const naCheckbox = document.getElementById('purchased_na');

    if (dateInput && naCheckbox) {
        naCheckbox.addEventListener('change', () => {
            dateInput.disabled = naCheckbox.checked;
            if (naCheckbox.checked) dateInput.value = ''; // clear date if N/A
        });
    }
</script>


<script>
function closePrinterModal() {
    document.getElementById('editPrinterModal').style.display = 'none';
}

function formatDateForInput(dateString) {
    if (!dateString) return '';

    const date = new Date(dateString);
    if (isNaN(date)) return ''; // Invalid date

    return date.toISOString().split('T')[0]; // Format: YYYY-MM-DD
}

// Attach event to all edit icons
document.querySelectorAll('.edit-printer-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.getAttribute('data-id');

        fetch('get_printer.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit-printer-id').value = data.id;
                document.getElementById('edit-printer_description').value = data.printer_description;
                document.getElementById('edit-printer_model').value = data.printer_model;
                document.getElementById('edit-printer_type').value = data.printer_type;
                document.getElementById('edit-printer_serialnumber').value = data.printer_serialnumber;
                document.getElementById('edit-printer_location').value = data.printer_location;
                document.getElementById('edit-printer_purchased_date').value = formatDateForInput(data.purchased_date);
                document.getElementById('edit-printer_ip_address').value = data.printer_ip_address || '';
                document.getElementById('edit-printer_subnet_mask').value = data.printer_subnet_mask || '';
                document.getElementById('edit-printer_gateway').value = data.printer_gateway || '';
                document.getElementById('edit-printer_dns1').value = data.printer_dns1 || '';
                document.getElementById('edit-printer_dns2').value = data.printer_dns2 || '';
                document.getElementById('edit-printer_switch').value = data.printer_switch || '';
                document.getElementById('edit-printer_port').value = data.printer_port || '';
                document.getElementById('edit-printer_web_portal_admin_credentials').value = data.printer_web_portal_admin_credentials || '';


                document.getElementById('editPrinterModal').style.display = 'block';
            })
            .catch(error => {
                alert('Failed to fetch printer details.');
                console.error(error);
            });
    });
});
</script>



<script>
function confirmDelete(printerId) {
    const confirmation = confirm("Are you sure you want to delete this printer?");
    if (confirmation) {
        // Redirect to the delete script with the printer ID
        window.location.href = `printer_inventory.php?delete_id=${printerId}`;
    }
}
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


<script>
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#printerTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>

</body>
</html>
