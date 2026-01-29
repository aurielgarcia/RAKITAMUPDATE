<?php
include 'db_connect.php'; // your SQL Server DB connection

$assetTypes = [
    'All In One PC',
    'Standard',
    'Engineering',
    'SolidWorks'
];

$statuses = ['In Stock - New', 'In Stock - Spare'];

function getAssetCount($conn, $type, $status) {
    $sql = "SELECT COUNT(*) AS count FROM dbo.it_asset_inventory 
            WHERE [device_type] = ? AND [install_status] = ?";
    $params = [$type, $status];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        echo "<pre>Error querying for $type ($status):\n";
        print_r(sqlsrv_errors());
        echo "</pre>";
        return 0;
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    return $row ? $row['count'] : 0;
}

function getTotalInStock($conn, $type, $statuses) {
    $total = 0;
    foreach ($statuses as $status) {
        $total += getAssetCount($conn, $type, $status);
    }
    return $total;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Asset Count | VERTIV GULF LLC ITMS</title>
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link rel="stylesheet" href="bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
      * {
    box-sizing: border-box;
}

#particles-js {
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: #ffffff;
    background-size: cover;
    background-position: center;
    z-index: 0;
}

body {
    margin: 0;
    padding: 0;
    font-family: 'Inter', sans-serif;
    color: black;
}

header {
    position: relative;
    z-index: 1;
    background-color: #1f2937;
    padding: 20px;
    color: white;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

h1 {
    margin: 0;
    font-weight: 600;
    font-size: 30px;
}

.title-link {
    color: white;
    text-decoration: none;
    transition: text-decoration 0.2s ease, opacity 0.2s ease;
}

.title-link:hover {
    text-decoration: underline;
    opacity: 0.9;
}

.container {
    position: relative;
    z-index: 1;
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 40px;
}

.card {
    margin-top: 40px;
    background: rgba(129, 125, 125, 0.05);
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    padding: 24px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    width: 250px;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

.card h2 {
    font-size: 25px;
    font-weight: bold;
    margin-bottom: 30px;
    color: #111827;
    text-align: center;
}

.status {
    font-size: 20px;
    margin-bottom: 8px;
    text-align: center;
}

.count {
    font-size: 20px;
    font-weight: bold;
    color: #3b82f6;
}

.total {
    margin-top: 30px;
    font-size: 25px;
    font-weight: 600;
    color: rgb(62, 168, 0);
    text-align: center;
}


table {
    background-color: white;
    border-radius: 0 0 10px 10px; /* only bottom corners rounded */
    overflow: hidden;
    margin-top: 0; /* Remove gap */
}

th {
    background-color: #1f2937 !important;
    color: white;
    font-size: 20px; 
}

td {
  font-weight: bold;   /* bold text */
  font-size: 18px;     /* font size */
}

td,
th {
    vertical-align: middle !important;
}

tbody tr:hover {
    background-color: #f3f4f6;
}

hr {
    margin: 60px 0;
    border-top: 1px solid #ccc;
}

.summary-header-bar {
    position: relative;
    width: 100%;
    background-color: #1f2937;
    color: white;
    text-align: center;
    padding: 15px 0;
    margin: 0; /* Remove bottom margin */
    box-sizing: border-box;
    border-top-left-radius: 8px;  /* Optional: rounded corners */
    border-top-right-radius: 8px; /* Optional: rounded corners */
}

.summary-header-bar h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}

.selected-row {
    background-color: #d1e7fd !important; /* Light blue highlight */
        }

    </style>
</head>
<body>
<div id="particles-js"></div>

<header>
    <h1><a href="it_asset_inventory.php" class="title-link">IT Asset Count</a></h1>
</header>

<div class="container">
    <!-- Cards Section -->
    <div class="grid">
        <?php foreach ($assetTypes as $type): ?>
            <div class="card">
                <h2><?= htmlspecialchars($type) ?></h2>
                <?php
                    $total = 0;
                    foreach ($statuses as $status):
                        $count = getAssetCount($conn, $type, $status);
                        $total += $count;
                ?>
                    <div class="status"><?= htmlspecialchars($status) ?>: <span class="count"><?= $count ?></span></div>
                <?php endforeach; ?>
                <div class="total">Total: <?= $total ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Summary Table Section -->
    <hr>
    <div class="summary-header-bar">
    <h2>IT Device Stocks</h2>
</div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Device Type</th>
                    <th>Required Quantity</th>
                    <th>In Stock Quantity</th>
                    <th>Requires PO</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assetTypes as $type): ?>
                    <?php
                        $inStockTotal = getTotalInStock($conn, $type, $statuses);

                        // Fetch saved required quantity from summary table
                        $sql = "SELECT required_quantity FROM dbo.it_asset_summary WHERE device_type = ?";
                        $params = [$type];
                        $stmt = sqlsrv_query($conn, $sql, $params);
                        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                        $requiredQty = $row ? $row['required_quantity'] : 0;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($type) ?></td>
                        <td class="required-qty"><?= htmlspecialchars($requiredQty) ?></td>
                        <td><?= $inStockTotal ?></td>
                        <td><?= $requiredQty - $inStockTotal ?></td>
                        <td><button class="btn btn-sm btn-primary edit-btn">Edit</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

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

// Inline editing with AJAX save and Requires PO calculation
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('tr');
        const deviceType = row.cells[0].textContent.trim();
        const qtyCell = row.querySelector('.required-qty');
        const inStockCell = row.cells[2];
        const poCell = row.cells[3];

        if (qtyCell.querySelector('input')) {
            // Save value
            const input = qtyCell.querySelector('input');
            const requiredQty = parseInt(input.value) || 0;
            const inStockQty = parseInt(inStockCell.textContent) || 0;

            qtyCell.textContent = requiredQty;
            poCell.textContent = requiredQty - inStockQty;
            this.textContent = 'Edit';

            // AJAX to save to database
            fetch('update_required_qty.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    deviceType: deviceType,
                    requiredQty: requiredQty
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') {
                    alert('❌ Error saving to database.');
                    console.error(data.message);
                }
            })
            .catch(err => console.error('AJAX Error:', err));

        } else {
            // Switch to edit mode
            const currentVal = qtyCell.textContent === '–' ? '' : qtyCell.textContent;
            qtyCell.innerHTML = `<input type="number" class="form-control form-control-sm" value="${currentVal}" min="0">`;
            this.textContent = 'Save';
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
