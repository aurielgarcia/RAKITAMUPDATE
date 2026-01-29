<?php
include 'db_connect.php';

// Fetch item descriptions from equipment table
$itemQuery = $conn->query("SELECT DISTINCT name FROM equipment ORDER BY name ASC");
$items = [];
while ($row = $itemQuery->fetch_assoc()) {
    $items[] = $row['name']; // Corrected from 'item_description' to 'name'
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] === 'other' ? $_POST['manual_name'] : $_POST['name'];
    $order_date = $_POST['order_date'];
    $quantity_ordered = $_POST['quantity_ordered'];
    $supplier = $_POST['supplier'];

    $sql = "INSERT INTO pending_purchases (name, order_date, quantity_ordered, supplier) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $name, $order_date, $quantity_ordered, $supplier);
    $stmt->execute();
    $stmt->close();
}

// Handle status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $status = $_GET['action'] === 'delivered' ? 'Delivered' : 'Canceled';
    $id = $_GET['id'];
    $stmt = $conn->prepare("UPDATE pending_purchases SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all pending purchases
$result = $conn->query("SELECT * FROM pending_purchases");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RAK - IT Equipment Pending Purchase Orders</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <style>
        /* Modal Styling */
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

<a href="dashboard.php" class="btn">← Back to IT Equipment Inventory</a>
<h2>IT Equipment<br>Pending Purchase Orders</h2>

<!-- Button to open form modal -->
<button id="openFormBtn" class="btn add-btn">+ Add Pending Purchase Order</button>

<!-- Modal form -->
<div id="formModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeFormBtn">&times;</span>
        <h3>Add New Pending Purchase Order</h3>
        <form method="POST" action="pending_purchase.php">
            <label>Item Description:</label>
            <select name="name" id="itemSelect" required>
                <option value="">-- Select Item --</option>
                <?php foreach ($items as $item): ?>
    <option value="<?php echo htmlspecialchars($item); ?>"><?php echo htmlspecialchars($item); ?></option>
<?php endforeach; ?>
                <option value="other">Other (Type Manually)</option>
            </select>

            <div id="manualInputWrapper" style="display: none;">
                <label>Enter Item Description:</label>
                <input type="text" name="manual_name" id="manualInput">
            </div>

            <label>Order Date:</label>
            <input type="date" name="order_date" required>

            <label>Quantity Ordered:</label>
            <input type="number" name="quantity_ordered" required>

            <label>Supplier:</label>
            <input type="text" name="supplier">

            <button type="submit" class="btn">Submit</button>
        </form>
    </div>
</div>

<br><br>

<table border="1" cellspacing="0" cellpadding="8">
    <thead>
        <tr>
            <th>Item Description</th>
            <th>Order Date</th>
            <th>Quantity Ordered</th>
            <th>Supplier</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo date('F-d-Y', strtotime($row['order_date'])); ?></td>
                <td><?php echo $row['quantity_ordered']; ?></td>
                <td><?php echo htmlspecialchars($row['supplier']); ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <?php if ($row['status'] === 'Pending'): ?>
                        <a href="pending_purchase.php?action=delivered&id=<?php echo $row['id']; ?>" class="btn">Delivered</a> |
                        <a href="pending_purchase.php?action=canceled&id=<?php echo $row['id']; ?>" class="btn delete-btn">Canceled</a>
                    <?php else: ?>
                        <em><?php echo $row['status']; ?></em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" class="no-records" style="text-align: center; font-style: italic;">No pending purchase orders found!</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Modal & Dropdown Logic -->
<script>
    const openBtn = document.getElementById('openFormBtn');
    const modal = document.getElementById('formModal');
    const closeBtn = document.getElementById('closeFormBtn');
    const itemSelect = document.getElementById('itemSelect');
    const manualInputWrapper = document.getElementById('manualInputWrapper');
    const manualInput = document.getElementById('manualInput');

    openBtn.onclick = () => modal.style.display = 'block';
    closeBtn.onclick = () => modal.style.display = 'none';
    window.onclick = (e) => {
        if (e.target == modal) modal.style.display = 'none';
    }

    itemSelect.addEventListener('change', function () {
        if (this.value === 'other') {
            manualInputWrapper.style.display = 'block';
            manualInput.required = true;
        } else {
            manualInputWrapper.style.display = 'none';
            manualInput.required = false;
        }
    });
</script>

</body>
</html>
