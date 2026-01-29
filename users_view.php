<?php

include('db_connect.php');

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $employee_id = trim($_POST['employee_id']);
    $department = trim($_POST['department']);
    $role = trim($_POST['role']);
    $status = trim($_POST['status']);

    if ($name && $employee_id && $department && $role && $status) {
        $insert_sql = "INSERT INTO itequip_inventory.users_profile (name, employee_id, department, role, status) VALUES (?, ?, ?, ?, ?)";
        $params = [$name, $employee_id, $department, $role, $status];
        $stmt_insert = sqlsrv_prepare($conn, $insert_sql, $params);
        if ($stmt_insert && sqlsrv_execute($stmt_insert)) {
            echo "<script>alert('User added successfully.');window.location.href='users_view.php';</script>";
            exit;
        } else {
            echo "<script>alert('Failed to add user.');</script>";
        }
    }
}

// Handle Return and Return Faulty actions
$actionMessage = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type'])) {
    $checkout_id = $_POST['checkout_id'];
    $action_type = $_POST['action_type'];

    if ($action_type === 'return') {
        $equipment_id = $_POST['equipment_id'];
        $quantity_checked_out = $_POST['quantity_checked_out'];

        $update_stock_sql = "UPDATE itequip_inventory.equipment SET stock_quantity = stock_quantity + ? WHERE id = ?";
        $stock_params = array($quantity_checked_out, $equipment_id);
        $stmt_stock = sqlsrv_prepare($conn, $update_stock_sql, $stock_params);
        if ($stmt_stock && sqlsrv_execute($stmt_stock)) {
            $update_status_sql = "UPDATE itequip_inventory.checkouts 
                          SET return_status = 'returned', 
                              return_date = GETDATE() 
                          WHERE id = ?";
            $status_params = array($checkout_id);
            $stmt_status = sqlsrv_prepare($conn, $update_status_sql, $status_params);
            if ($stmt_status && sqlsrv_execute($stmt_status)) {
                $actionMessage = "Item successfully returned.";
            }
        }
    } elseif ($action_type === 'faulty') {
        $update_status_sql = "UPDATE itequip_inventory.checkouts SET return_status = 'returned_faulty' WHERE id = ?";
        $status_params = array($checkout_id);
        $stmt_status = sqlsrv_prepare($conn, $update_status_sql, $status_params);
        if ($stmt_status && sqlsrv_execute($stmt_status)) {
            $actionMessage = "Item marked as returned faulty.";
        }
    }
}

// Filters
$filter = $_GET['filter'] ?? '';
$search = trim($_GET['search'] ?? '');
$filter_query = '';
$params = [];

if ($filter !== '' && preg_match('/^[A-Z]$/', $filter)) {
    $filter_query .= "AND up.name LIKE ?";
    $params[] = $filter . '%';
}

if (!empty($search)) {
    $filter_query .= " AND (
        up.name LIKE ? OR
        up.employee_id LIKE ? OR
        up.department LIKE ? OR
        up.role LIKE ? OR
        up.status LIKE ?
    )";
    for ($i = 0; $i < 5; $i++) {
        $params[] = '%' . $search . '%';
    }
}

$sql = "
SELECT 
    up.name AS requested_by,
    up.employee_id,
    up.department,
    up.role,
    up.status,
    c.id AS checkout_id,
    c.equipment_id,
    c.equipment_name,
    c.model,
    c.quantity_checked_out,
    c.checkout_date,
    c.service_now_request_ticket,
    c.return_status
FROM itequip_inventory.users_profile up
LEFT JOIN itequip_inventory.checkouts c ON c.requested_by = up.name AND c.checkout_date IS NOT NULL
WHERE up.status IN ('Active', 'Resigned') $filter_query
ORDER BY up.name, c.checkout_date DESC
";

$stmt = sqlsrv_query($conn, $sql, $params);
if (!$stmt) die(print_r(sqlsrv_errors(), true));

$users = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $requestor = $row['requested_by'];
    if (!isset($users[$requestor])) {
        $users[$requestor] = [
            'employee_id' => $row['employee_id'],
            'department' => $row['department'],
            'role' => $row['role'],
            'status' => $row['status'],
            'items' => []
        ];
    }
    if (!empty($row['checkout_id'])) {
        $users[$requestor]['items'][] = $row;
        }
}
?>

<!-- HTML Rendering Part -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Users View | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('.user-card').each(function() {
                    var text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(value) > -1);
                });
            });
        });
    </script>
    <style>

        body {
  background-color:rgb(206, 215, 230); /* Change to your desired color */
}

        h2 {
           text-align: center;
        }

        h4 {
        font-size: 1.2rem;
        font-weight: 700;
        text-align: center;
        color: #333;
        margin: 10px auto 5px;
        padding-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 1px;
        }

        h5 {
        font-size: 1.2rem;
        font-weight: 700;
        text-align: center;
        color: #333;
        margin: 40px auto 5px;
        padding-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 1px;
        }

        .search-bar-container {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

.filter-bar {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

           .table-wrapper {
            max-height: calc(104vh - 200px);
            overflow-y: auto;
            overflow-x: auto;
            border: 1px solid #ddd;
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
            border: .5px solid #ccc; /* adds grid lines */
        }

        table#assetTable th {
            position: sticky;
            top: 0;
            background-color: #343a40;
            color: white;
            z-index: 1;
        }

        table#assetTable {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            table-layout: auto;
        }

        table#assetTable th,
        table#assetTable td {
            padding: 10px;
            text-align: left;
            word-wrap: break-word;
            border-right: 1px solid #ccc;
        }


        .search-bar input[type="text"] { 
            padding: 8px; width: 300px; 
            font-size: 1em; 
        }

        .filter-bar a {
            display: inline-block; 
            margin: 0 5px 10px 0; 
            padding: 5px 10px;
            background: #dcdde1; 
            color: #2f3640; 
            border-radius: 5px; 
            text-decoration: none;
        }

        .filter-bar a.active {
             background: #40739e; 
             color: white; 
             font-weight: bold; 
            }

        .user-card {
            background: #fff; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .profile-header { 
            display: flex; 
            align-items: center; 
            margin-bottom: 15px; 
        }

        .avatar { 
            width: 64px; 
            height: 64px; 
            border-radius: 50%; 
            background: #ccc; 
            margin-right: 15px; 
        }

        .info h3 { 
            margin: 0; 
            font-size: 1.2em; 
        }

        .badge { 
            color: white; 
            padding: 4px 10px; 
            border-radius: 12px; 
            font-size: 0.8em; 
        }

        .badge.active {
            background: #4cd137; /* Green */
        }

        .badge.resigned {
            background: #e84118; /* Red */
        }

        .action-buttons form { 
            display: inline; }

        .add-user-btn {
            background: #27ae60; color: white; padding: 10px 20px; border: none;
            border-radius: 5px; font-size: 1em; cursor: pointer; float: right;
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

        /* Modal */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 10; 
            left: 0; 
            top: 0;
            width: 100%; 
            height: 100%; 
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff; 
            margin: 10% auto; 
            padding: 20px;
            border-radius: 8px; 
            width: 400px; 
            position: relative;
        }

        .close {
            position: absolute; top: 10px; right: 15px;
            font-size: 24px; cursor: pointer; color: #888;
        }

        .modal-content input, .modal-content select {
            width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc;
        }

        table#assetTable th:nth-child(1),
        table#assetTable th:nth-child(2),
        table#assetTable th:nth-child(3),
        table#assetTable th:nth-child(4),
        table#assetTable th:nth-child(5) {
            text-align: center;
        }

        table#assetTable td:nth-child(1),
        table#assetTable td:nth-child(2),
        table#assetTable td:nth-child(3),
        table#assetTable td:nth-child(4) {
            text-align: center;
        }

        table#equipmentTable th:nth-child(1),
        table#equipmentTable th:nth-child(2),
        table#equipmentTable th:nth-child(3),
        table#equipmentTable td:nth-child(3),
        table#equipmentTable th:nth-child(4),
        table#equipmentTable th:nth-child(5),
        table#equipmentTable td:nth-child(5),
        table#equipmentTable th:nth-child(6),
        table#equipmentTable td:nth-child(6) {
            text-align: center;
        }

         table#equipmentTable td:nth-child(4) {
            text-align: right;
        }

        th:nth-child(1), td:nth-child(1) { width: 18%; }
        th:nth-child(2), td:nth-child(2) { width: 15%; }
        th:nth-child(3), td:nth-child(3) { width: 8%; }
        th:nth-child(4), td:nth-child(4) { width: 10%; }
        th:nth-child(5), td:nth-child(5) { width: 20%; }
        th:nth-child(6), td:nth-child(6) { width: 15%; }

form {
    background: none !important;
    box-shadow: none !important;
    margin: 0;
    padding: 0;
    border: none;
}

.btn-return_resign {
  background-color: #28a745; /* green */
  color: white;
  border: none;
  padding: 10px 15px;
  font-size: .9rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
  /* add a subtle box-shadow for nicer effect */
  box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}

.btn-return_resign:hover {
  background-color: #218838; /* darker green on hover */
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

.btn-return_resign:disabled {
  background-color: #6c757d; /* gray when disabled */
  cursor: not-allowed;
  box-shadow: none;
  transform: none;
}

.btn-return, .btn-faulty {
    width: 100px;
    padding: 8px 0;
    font-size: 14px;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    gap: 0;
}

.btn-return {
    background-color: #28a745;
}

.btn-faulty {
    background-color: #dc3545; 
}

.avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #ccc;
    margin-right: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: black;
}

    </style>
</head>
<body>
<div class="sidebar">
    <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="returned_it_asset_records.php"><i class="material-icons">arrow_right</i>Returned IT Asset</a>
    <a href="returned_desk_phone_records.php"><i class="material-icons">arrow_right</i>Returned Desk Phone</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>



<div class="main-content">
<?php if ($actionMessage): ?>
<script>alert("<?= $actionMessage ?>");</script>
<?php endif; ?>


<button class="add-user-btn" onclick="document.getElementById('addUserModal').style.display='block'">Add User</button>

<div class="search-bar-container">
    <div class="search-bar">
<input type="text" id="searchInput" placeholder="Search users...">
</div>
</div>

<div class="filter-bar">
    <?php foreach (range('A', 'Z') as $letter): ?>
        <a href="?filter=<?= $letter ?>" class="<?= ($filter === $letter) ? 'active' : '' ?>">
            <?= $letter ?>
        </a>
    <?php endforeach; ?>
    <a href="users_view.php" class="<?= ($filter === '') ? 'active' : '' ?>">All</a>
</div>

<?php foreach ($users as $name => $user): ?>
    <div class="user-card">
        <div class="profile-header">
            <div class="avatar">
    <span class="material-icons">person</span>
</div>
            <div class="info">
                <h3><?= htmlspecialchars($name) ?></h3>
                <p>
                    ID: <?= htmlspecialchars($user['employee_id']) ?> • 
                    <?= htmlspecialchars($user['department']) ?> • 
                    <?= htmlspecialchars($user['role']) ?> • 
                    <?php 
                        $statusClass = strtolower($user['status']); // e.g. 'Active' → 'active'
                    ?>
                    <span class="badge <?= $statusClass ?>">
                        <?= htmlspecialchars($user['status']) ?>
                    </span>
                </p>
            </div>

            <!-- Make button align right -->
    <div style="margin-left: auto;">
        <button class="edit-user-btn" data-name="<?= htmlspecialchars($name) ?>"
            data-employee_id="<?= htmlspecialchars($user['employee_id']) ?>"
            data-department="<?= htmlspecialchars($user['department']) ?>"
            data-role="<?= htmlspecialchars($user['role']) ?>"
            data-status="<?= htmlspecialchars($user['status']) ?>"
            style="background-color:#007bff; color:#fff; border:none; padding:6px 12px; border-radius:5px; cursor:pointer;">
            Edit
        </button>
    </div>
        </div>

     <div class="table-wrapper">
        <h4>Assigned IT Asset</h4>
    <table class="item-table" id="assetTable">
        <thead>
            <tr>
                <th>Device Model</th>
                <th>Device Type</th>
                <th>Service Tag</th>
                <th>ServiceNow Ticket</th>

                <th>Action</th>
            </tr>
        </thead>
        <tbody>
<?php
$asset_sql = "
    SELECT device_model, serial_number, device_type, service_now_request_ticket 
    FROM dbo.it_asset_inventory 
    WHERE LTRIM(RTRIM(LOWER(assigned_to))) = LTRIM(RTRIM(LOWER(?)))
";
$asset_stmt = sqlsrv_query($conn, $asset_sql, [strtolower(trim($name))]);

if ($asset_stmt && sqlsrv_has_rows($asset_stmt)) {
    while ($asset = sqlsrv_fetch_array($asset_stmt, SQLSRV_FETCH_ASSOC)) {
        $serial = $asset['serial_number'] ?? '';
        $model = $asset['device_model'] ?? '';
        $type = $asset['device_type'] ?? '';
        $employee = ''; // fallback

        // Get employee name from serial number
        if (!empty($serial)) {
            $emp_query = "SELECT assigned_to FROM dbo.it_asset_inventory WHERE serial_number = ?";
            $emp_stmt = sqlsrv_query($conn, $emp_query, [$serial]);
            if ($emp_stmt && $row = sqlsrv_fetch_array($emp_stmt, SQLSRV_FETCH_ASSOC)) {
                $employee = $row['assigned_to'] ?? '';
            }
        }

        // Escape for HTML safety
        $serialEsc = htmlspecialchars($serial);
        $employeeEsc = htmlspecialchars($employee);
        $modelEsc = htmlspecialchars($model);
        $typeEsc = htmlspecialchars($type);
        $ticketEsc = htmlspecialchars($asset['service_now_request_ticket'] ?? '');

        echo "<tr>";
        echo "<td>$modelEsc</td>";
        echo "<td>$typeEsc</td>";
        echo "<td>$serialEsc</td>";
        echo "<td>$ticketEsc</td>";
        echo "<td style='text-align: center;'>
                <button 
                    class='btn btn-sm btn-return_resign return-btn' 
                    data-serial='$serialEsc' 
                    data-employee='$employeeEsc' 
                    data-model='$modelEsc' 
                    data-type='$typeEsc'>
                    Return - Resign
                </button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' style='text-align:center;'>No assigned IT Asset for this user.</td></tr>";
}
?>
</tbody>
    </table>

    
    <div class="table-wrapper">
        <h5>Assigned IT Equipment</h5>
        <table class="item-table" id="equipmentTable">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Device Model</th>
                    <th>Quantity</th>
                    <th>Checkout Date</th>
                    <th>ServiceNow Ticket</th>
                    
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
<?php if (!empty($user['items'])): ?>
    <?php foreach ($user['items'] as $item): ?>
        <tr>
            <td><?= $item['equipment_name'] ?></td>
            <td><?= $item['model'] ?></td>
            <td style="text-align:center;"><?= $item['quantity_checked_out'] ?></td>
            <td><?= $item['checkout_date'] ? $item['checkout_date']->format('M-d-Y') : '' ?></td>
            <td><?= htmlspecialchars($item['service_now_request_ticket']) ?></td>
            <td>
                <?php if ($item['return_status'] === 'returned'): ?>
                    <span style="color: green; font-weight: bold;">Returned</span>
                <?php elseif ($item['return_status'] === 'returned_faulty'): ?>
                    <span style="color: red; font-weight: bold;">Returned Faulty</span>
                <?php else: ?>
                <div style="display: flex; justify-content: center; gap: 2px;">
                    <form method="post">
                        <input type="hidden" name="checkout_id" value="<?= $item['checkout_id']; ?>">
                        <input type="hidden" name="equipment_id" value="<?= $item['equipment_id']; ?>">
                        <input type="hidden" name="quantity_checked_out" value="<?= $item['quantity_checked_out']; ?>">
                        <input type="hidden" name="action_type" value="return">
                        <button type="submit" class="btn-return">Return</button>
                    </form>

                    <form method="post">
                        <input type="hidden" name="checkout_id" value="<?= $item['checkout_id']; ?>">
                        <input type="hidden" name="action_type" value="faulty">
                        <button type="submit" class="btn-faulty">Return Faulty</button>
                    </form>
                </div>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6" style="text-align:center;">No assigned IT Equipment for this user.</td></tr>
<?php endif; ?>
</tbody>

        </table>
    </div>

</div>                   
    </div>
<?php endforeach; ?>
    </div>



<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addUserModal').style.display='none'">&times;</span>
<form method="POST">
    <h2>Add User</h2>
    <input type="text" name="name" placeholder="Name" required>
    <input type="text" name="employee_id" placeholder="Employee ID" required>
    <input type="text" name="department" placeholder="Department" required>
    <input type="text" name="role" placeholder="Role" required>
    <select name="status" required>
        <option value="Active">Active</option>
        <option value="Inactive">Resigned</option>
    </select>

    <div class="button-wrapper">
        <button type="submit" name="add_user">Submit</button>
    </div>
</form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
  <div class="modal-content">
    <span class="close" id="editUserClose">&times;</span>
    <h2>Edit User</h2>
    <form id="editUserForm" method="POST" action="edit_user.php">
      <input type="hidden" name="original_employee_id" id="edit_original_employee_id" />
      
      <label for="edit_name">User</label>
      <input type="text" id="edit_name" name="name" required />

      <label for="edit_employee_id">Employee ID</label>
      <input type="text" id="edit_employee_id" name="employee_id" required />

      <label for="edit_department">Department</label>
      <input type="text" id="edit_department" name="department" required />

      <label for="edit_role">Role</label>
      <input type="text" id="edit_role" name="role" required />

      <label for="edit_status">Status</label>
      <select name="status" id="edit_status" required>
  <option value="Active">Active</option>
  <option value="Resigned">Resigned</option>
</select>

      <div class="button-wrapper">
      <button type="submit" style="background-color:#007bff;color:white;padding:8px 16px;border:none;border-radius:5px;cursor:pointer;">
        Save Changes
      </button>
      </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function() {
    // Open Edit Modal and populate data
    $('.edit-user-btn').on('click', function() {
        $('#edit_original_employee_id').val($(this).data('employee_id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_employee_id').val($(this).data('employee_id'));
        $('#edit_department').val($(this).data('department'));
        $('#edit_role').val($(this).data('role'));
        $('#edit_status').val($(this).data('status'));

        $('#editUserModal').show();
    });

    // Close modal on clicking X
    $('#editUserClose').on('click', function() {
        $('#editUserModal').hide();
    });

    // Close modal when clicking outside modal content
    $(window).on('click', function(event) {
        if ($(event.target).is('#editUserModal')) {
            $('#editUserModal').hide();
        }
    });
});

</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-return_resign').forEach(button => {
    button.addEventListener('click', function () {
      const userConfirmed = confirm("Do you want to return the IT Asset?");
      if (!userConfirmed) return; // Stop if user clicks Cancel

      const serialNumber = this.getAttribute('data-serial');
      const employeeName = this.getAttribute('data-employee');
      const deviceModel = this.getAttribute('data-model');
      const deviceType = this.getAttribute('data-type');

      fetch('returned_resigned_it_asset.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          serial_number: serialNumber,
          employee_name: employeeName,
          device_model: deviceModel,
          device_type: deviceType
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          this.textContent = 'Returned - Resigned';
          this.classList.remove('btn-warning');
          this.classList.add('btn-success');
          this.disabled = true;
          alert('Asset successfully marked as returned.');
        } else {
          alert('Failed to update asset status: ' + data.message);
        }
      })
      .catch(err => {
        console.error('Fetch error:', err);
        alert('Error connecting to the server.');
      });
    });
  });
});
</script>


</body>
</html>
