<?php
// Include your database connection
include 'db_connect.php'; // make sure this connects to your SQL Server using sqlsrv

// Fetch recent checkout records with not printed or NULL status ONLY
$sql = "SELECT id, requested_by, equipment_name, model, service_now_request_ticket 
        FROM itequip_inventory.checkouts 
        WHERE print_status IS NULL OR print_status != 'printed'
        ORDER BY id DESC";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sqlAssetInventory = "SELECT id, assigned_to, device_model, serial_number, service_now_request_ticket
      FROM dbo.it_asset_inventory
      WHERE print_status IS NULL
      AND assigned_to IS NOT NULL
      AND LTRIM(RTRIM(assigned_to)) != ''
      ORDER BY assigned_to ASC";

$stmtInventory = sqlsrv_query($conn, $sqlAssetInventory);

if ($stmtInventory === false) {
    die(print_r(sqlsrv_errors(), true));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Stock Check-Out Menu | VERTIV GULF LLC ITMS</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="bootstrap.min.css">
  <link rel="icon" type="image/png" href="favicon.jpg">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <style>

    #particles-js {
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        background-size: cover;
        background-position: center;
        z-index: 0;
    }

.sidebar-divider {
  border: none;
  height: 1px;
  background-color: white;
  margin: 5px;
}


    .content {
      position: relative;
      z-index: 1;
      height: 90%;
      margin-left: 220px;
      padding: 20px;
    }

    #welcomeMessage {
      text-align: center;
      margin-top: 50px;
      color: #333;
      font-size: 20px;
    }

    .table-container {
      margin-top: 40px;
      background: rgb(242, 240, 240);
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgb(0, 0, 0);
    }

    table {
      font-size: 15px;
    }


        table#equipmentTable th:nth-child(1),
        table#equipmentTable th:nth-child(2),
        table#equipmentTable th:nth-child(3),
        table#equipmentTable th:nth-child(4),
        table#equipmentTable th:nth-child(5) {
            text-align: center;
        }


        table#equipmentTable td:nth-child(1),
        table#equipmentTable td:nth-child(2) {
            text-align: left;
            text-indent: 20px;
        }


        th:nth-child(1), td:nth-child(1) { width: 15%; }
        th:nth-child(2), td:nth-child(2) { width: 15%; }
        th:nth-child(3), td:nth-child(3) { width: 10%; }
        th:nth-child(4), td:nth-child(4) { width: 10%; }
        th:nth-child(5), td:nth-child(5) { width: 8%; }

  </style>
</head>
<body>

<div id="particles-js"></div>

<div class="sidebar">
  <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">

  <hr class="sidebar-divider">

  <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
  <a href="it_asset_checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out IT Asset</a>
  <a href="checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out IT Equipment</a>
  <a href="toner_drum_checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out Printer Toner and Drum</a>
  <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="content">
  <div id="welcomeMessage">
    <h1>Welcome to Stock Check-out!</h1>
    <p>Select a category from the sidebar to start the check-out process</p>
  </div>

  <div class="table-container">
    <h3 class="text-center mb-4">For Releasing IT Equipment</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="equipmentTable">
        <thead class="table-dark">
          <tr>
            <th>User</th>
            <th>Item Needed</th>
            <th>Device Model</th>
            <th>ServiceNow Request Ticket</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="checkout-table-body">
  <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
    <tr data-id="<?= $row['id'] ?>">
      <td><?= htmlspecialchars($row['requested_by']) ?></td>
      <td><?= htmlspecialchars($row['equipment_name']) ?></td>
      <td><?= htmlspecialchars($row['model']) ?></td>
      <td><?= htmlspecialchars($row['service_now_request_ticket']) ?></td>
      <td>
        <button class="print-btn btn btn-sm btn-success">Print</button>
      </td>
    </tr>
  <?php endwhile; ?>
</tbody>
      </table>
    </div>
  </div>


<div class="table-container">
  <h3 class="text-center mb-4">For Releasing IT Asset</h3>
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="equipmentTable">
      <thead class="table-dark">
        <tr>
          <th>Assigned To</th>
          <th>Device Model</th>
          <th>Service Tag</th>
          <th>ServiceNow Request Ticket</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="asset-table-body">
  <?php while ($row = sqlsrv_fetch_array($stmtInventory, SQLSRV_FETCH_ASSOC)) : ?>
    <tr data-id="<?= $row['id'] ?>">
      <td><?= htmlspecialchars($row['assigned_to']) ?></td>
      <td><?= htmlspecialchars($row['device_model']) ?></td>
      <td><?= htmlspecialchars($row['serial_number']) ?></td>
      <td><?= htmlspecialchars($row['service_now_request_ticket']) ?></td>
      <td>
        <button class="asset-print-btn btn btn-sm btn-success">Print</button>
      </td>
    </tr>
  <?php endwhile; ?>
</tbody>
    </table>
  </div>
</div>
</div>

<!-- Particles.js library -->
<script src="js/particles.min.js"></script>
<script>
  particlesJS("particles-js", {
    "particles": {
      "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
      "color": { "value": "#000000" },
      "shape": { "type": "circle", "stroke": { "width": 0 }, "polygon": { "nb_sides": 5 } },
      "opacity": { "value": 0.3 },
      "size": { "value": 3, "random": true },
      "line_linked": { "enable": true, "distance": 150, "color": "#000000", "opacity": 0.2, "width": 1 },
      "move": { "enable": true, "speed": 2, "out_mode": "out" }
    },
    "interactivity": {
      "events": { "onhover": { "enable": true, "mode": "grab" }, "resize": true },
      "modes": { "grab": { "distance": 140, "line_linked": { "opacity": 0.4 } } }
    },
    "retina_detect": true
  });
</script>

<script>
let printWindow = null;

document.getElementById('checkout-table-body').addEventListener('click', function(event) {
    if (event.target.classList.contains('print-btn')) {
        const tr = event.target.closest('tr');
        const checkoutId = tr.getAttribute('data-id');

        if (printWindow && !printWindow.closed) {
            printWindow.focus();
            printWindow.location.href = 'print_checkout.php?id=' + checkoutId;
        } else {
            printWindow = window.open('print_checkout.php?id=' + checkoutId, 'printWindow', 'width=600,height=400');
        }
    }
});
</script>

<script>
let assetPrintWindow = null;

document.getElementById('asset-table-body').addEventListener('click', function(event) {
    if (event.target.classList.contains('asset-print-btn')) {
        const tr = event.target.closest('tr');
        const assetId = tr.getAttribute('data-id');

        // Open or reuse the print window
        if (assetPrintWindow && !assetPrintWindow.closed) {
            assetPrintWindow.focus();
            assetPrintWindow.location.href = 'print_it_asset_checkout.php?id=' + assetId;
        } else {
            assetPrintWindow = window.open('print_it_asset_checkout.php?id=' + assetId, 'assetPrintWindow', 'width=600,height=400');
        }
    }
});
</script>



</body>
</html>