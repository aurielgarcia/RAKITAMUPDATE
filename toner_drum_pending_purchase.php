<?php
include('db_connect.php');

// Handle Delivered or Canceled actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];

    if ($action === 'delivered') {
        $status = 'Delivered';
    } elseif ($action === 'canceled') {
        $status = 'Canceled';
    }

    if (isset($status)) {
        $update = $conn->prepare("UPDATE toner_drum_pending_purchases SET status = ? WHERE id = ?");
        $update->bind_param("si", $status, $id);
        $update->execute();
        $update->close();

        header("Location: toner_drum_pending_purchase.php");
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = $_POST['model']; // This comes from final hidden input
    $color = $_POST['color'];
    $type = $_POST['type'];
    $order_date = $_POST['order_date'];
    $quantity_ordered = $_POST['quantity_ordered'];
    $supplier = $_POST['supplier'];
    $status = 'Pending';

    $stmt = $conn->prepare("INSERT INTO toner_drum_pending_purchases (model, color, type, order_date, quantity_ordered, supplier, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiss", $model, $color, $type, $order_date, $quantity_ordered, $supplier, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: toner_drum_pending_purchase.php");
    exit;
}

// Fetch pending purchase orders
$query = "SELECT * FROM toner_drum_pending_purchases ORDER BY order_date DESC";
$result = mysqli_query($conn, $query);

// Fetch models from inventory
$inventoryModelsResult = mysqli_query($conn, "SELECT DISTINCT model FROM toner_drum_inventory ORDER BY model ASC");
$inventoryModels = [];
while ($row = mysqli_fetch_assoc($inventoryModelsResult)) {
    $inventoryModels[] = $row['model'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RAK - Printer Toner & Drum - Pending Purchase Orders</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <style>
        h2 {
            text-align: center;
            font-size: 2rem;
            margin-top: 30px;
            margin-bottom: 10px;
            color: #333;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            animation: colorChange 5s infinite;
            font-weight: bold;
        }

        @keyframes colorChange {
            0%   { color: magenta; }
            25%  { color: hotpink; }
            50%  { color: yellow; }
            75%  { color: cyan; }
            100% { color: black; }
        }

        .color-box {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 1px solid #ccc;
            margin-right: 5px;
            vertical-align: middle;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
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
            border-radius: 8px;
            max-width: 400px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .modal-content input,
        .modal-content select {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .modal-content button {
            margin-top: 10px;
            width: 100%;
        }

        .close-btn {
            color: #aaa;
            position: absolute;
            top: 12px;
            right: 20px;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover {
            color: black;
        }
    </style>
</head>
<body>

<a href="rak_toner_drum_inventory.php" class="btn">← Toner & Drum Inventory</a>

<h2>Printer Toner & Drum<br>Pending Purchase Orders</h2>

<!-- Button to open modal -->
<button id="openFormBtn" class="btn add-btn">+ Add Pending Purchase Order</button>

<!-- Modal Form -->
<div id="formModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeFormBtn">&times;</span>
        <h3>Add New Pending Purchase Order</h3>
        <form method="POST" action="toner_drum_pending_purchase.php" onsubmit="return handleModelSubmission();">
            <label for="model_dropdown">Model:</label>
            <select name="model_dropdown" id="model_dropdown" required onchange="toggleManualInput(this)">
                <option value="">-- Select Model --</option>
                <?php foreach ($inventoryModels as $invModel): ?>
                    <option value="<?= htmlspecialchars($invModel) ?>"><?= htmlspecialchars($invModel) ?></option>
                <?php endforeach; ?>
                <option value="other">Other (Type Manually)</option>
            </select>

            <input type="text" name="model_manual" id="model_manual" placeholder="Type model here..." style="display:none; margin-top: 5px;">

            <label for="color">Color:</label>
            <select name="color" required>
                <option value="">-- Select Color --</option>
                <option value="Black">Black</option>
                <option value="Cyan">Cyan</option>
                <option value="Yellow">Yellow</option>
                <option value="Magenta">Magenta</option>
                <option value="Tri-Color">Tri-Color</option>
            </select>

            <label for="type">Type:</label>
            <select name="type" required>
                <option value="">-- Select Type --</option>
                <option value="Toner">Toner</option>
                <option value="Drum">Drum</option>
                <option value="Cartridge">Cartridge</option>
            </select>

            <label for="order_date">Order Date:</label>
            <input type="date" name="order_date" required>

            <label for="quantity_ordered">Quantity Ordered:</label>
            <input type="number" name="quantity_ordered" required>

            <label for="supplier">Supplier:</label>
            <input type="text" name="supplier" required>

            <input type="hidden" name="model" id="final_model">

            <button type="submit" class="btn">Submit</button>
        </form>
    </div>
</div>

<!-- Table -->
<table>
    <thead>
        <tr>
            <th>Model</th>
            <th>Color</th>
            <th>Type</th>
            <th>Order Date</th>
            <th>Quantity Ordered</th>
            <th>Supplier</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['model']) ?></td>
                    <td>
                        <div class="color-box" style="background-color: <?= htmlspecialchars($row['color']) ?>;"></div>
                        <?= htmlspecialchars($row['color']) ?>
                    </td>
                    <td><?= htmlspecialchars($row['type']) ?></td>
                    <td><?= date('F-d-Y', strtotime($row['order_date'])) ?></td>
                    <td><?= (int)$row['quantity_ordered'] ?></td>
                    <td><?= htmlspecialchars($row['supplier']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'Pending'): ?>
                            <a href="toner_drum_pending_purchase.php?action=delivered&id=<?= $row['id']; ?>" class="btn">Delivered</a> |
                            <a href="toner_drum_pending_purchase.php?action=canceled&id=<?= $row['id']; ?>" class="btn delete-btn">Canceled</a>
                        <?php else: ?>
                            <em><?= htmlspecialchars($row['status']) ?></em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="no-records" style="text-align: center; font-style: italic;">No pending purchase orders found!</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Scripts -->
<script>
    const openBtn = document.getElementById('openFormBtn');
    const modal = document.getElementById('formModal');
    const closeBtn = document.getElementById('closeFormBtn');

    openBtn.onclick = () => modal.style.display = 'block';
    closeBtn.onclick = () => modal.style.display = 'none';
    window.onclick = (e) => {
        if (e.target == modal) modal.style.display = 'none';
    }

    function toggleManualInput(select) {
        const manualInput = document.getElementById('model_manual');
        if (select.value === 'other') {
            manualInput.style.display = 'block';
            manualInput.required = true;
        } else {
            manualInput.style.display = 'none';
            manualInput.required = false;
        }
    }

    function handleModelSubmission() {
        const dropdown = document.getElementById('model_dropdown');
        const manual = document.getElementById('model_manual');
        const finalModelInput = document.getElementById('final_model');

        finalModelInput.value = dropdown.value === 'other' ? manual.value : dropdown.value;
        return true;
    }
</script>

</body>
</html>
