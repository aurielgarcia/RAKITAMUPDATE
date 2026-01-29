<?php
include('db_connect.php');

// Fetch existing equipment names
$equipment_names = [];
$equipment_query = "SELECT DISTINCT name FROM itequip_inventory.equipment ORDER BY name ASC";
$equipment_result = sqlsrv_query($conn, $equipment_query);
if ($equipment_result !== false) {
    while ($row = sqlsrv_fetch_array($equipment_result, SQLSRV_FETCH_ASSOC)) {
        $equipment_names[] = $row['name'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] === 'other' ? $_POST['custom_name'] : $_POST['name'];
    $model = isset($_POST['custom_model']) && !empty(trim($_POST['custom_model'])) ? $_POST['custom_model'] : $_POST['model'];
    $order_date = $_POST['order_date'];
    $quantity_ordered = (int)$_POST['quantity_ordered'];
    $delivery_date = $_POST['delivery_date'];
    $quantity_delivered = (int)$_POST['quantity_delivered'];
    $status = $_POST['status'];
    $supplier = $_POST['supplier'];

    // Check if equipment with both name AND model exists
    $check_sql = "SELECT id, stock_quantity FROM itequip_inventory.equipment WHERE name = ? AND model = ?";
    $check_stmt = sqlsrv_query($conn, $check_sql, [$name, $model]);

    if ($check_stmt && sqlsrv_has_rows($check_stmt)) {
        $row = sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC);
        $equipment_id = $row['id'];
        $new_stock_quantity = $row['stock_quantity'] + $quantity_delivered;
        $new_status = $new_stock_quantity > 0 ? 'Available' : 'Need to Order';

        $update_sql = "UPDATE itequip_inventory.equipment SET stock_quantity = ?, status = ? WHERE id = ?";
        sqlsrv_query($conn, $update_sql, [$new_stock_quantity, $new_status, $equipment_id]);
    } else {
        // Insert new equipment
        $insert_equipment_sql = "INSERT INTO itequip_inventory.equipment (name, model, storage_location, stock_quantity, status)
                                 VALUES (?, ?, 'Default Location', ?, 'Available')";
        sqlsrv_query($conn, $insert_equipment_sql, [$name, $model, $quantity_delivered]);

        // Get newly inserted equipment ID
        $get_id_sql = "SELECT id FROM itequip_inventory.equipment WHERE name = ? AND model = ?";
        $get_id_stmt = sqlsrv_query($conn, $get_id_sql, [$name, $model]);
        $row = sqlsrv_fetch_array($get_id_stmt, SQLSRV_FETCH_ASSOC);
        $equipment_id = $row['id'];
    }

    // Insert check-in record
    $insert_checkin_sql = "INSERT INTO itequip_inventory.checkins 
        (equipment_id, name, model, order_date, quantity_ordered, delivery_date, quantity_delivered, status, supplier)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [$equipment_id, $name, $model, $order_date, $quantity_ordered, $delivery_date, $quantity_delivered, $status, $supplier];

    $insert_result = sqlsrv_query($conn, $insert_checkin_sql, $params);

    if ($insert_result) {
    echo "<script>
        alert('IT Equipment Stock Check-in successful!');
        window.location.href = 'checkin.php';
    </script>";
} else {
    $errors = sqlsrv_errors();
    echo "<p class='error-message'>Error inserting record: " . $errors[0]['message'] . "</p>";
}
}
?>

<!-- HTML BELOW REMAINS MOSTLY UNCHANGED -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Equipment Stock Check-in | VERTIV GULF LLC ITMS ITMS</title>
    <link rel="stylesheet" href="styles.css">
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

        .main-content {
            margin-left: 200px;
            padding: 20px;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .tab-header {
            background: rgba(44, 62, 80, 0.6);
            color: white;
            padding: 20px 30px;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
        }

        form {
            background: rgba(152, 199, 246, 0.6);;;
            margin: 20px auto;
            max-width: 500px;
            width: 100%;
        }

        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
            color: #2c3e50;
            font-size: 15px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            margin-bottom: 12px;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus, select:focus {
            border-color: #3498db;
            outline: none;
        }

        .btn {
            background-color: rgb(91, 160, 13);
            color: white;
            font-weight: bold;
            padding: 12px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 30%;
            margin: 20px auto 0;
            display: block;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background-color: rgb(4, 115, 15);
        }

        #name option,
        #model option,
        #status option,
        #supplier option {
    text-align: left;
        }
    </style>
    </head>
<body>

<div id="particles-js"></div>

<div class="sidebar">
    <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="it_asset_checkin.php"><i class="material-icons">arrow_right</i>Stock Check-in IT Asset</a>
    <a href="toner_drum_checkin.php"><i class="material-icons">arrow_right</i>Stock Check-in Printer Toner and Drum </a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">IT Equipment Stock Check-in</div>

    <form action="checkin.php" method="POST">
        <label for="name">Item Description:</label>
        <select name="name" id="name" onchange="toggleCustomName()" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Item Description --</option>
            <?php foreach ($equipment_names as $equipment): ?>
                <option value="<?= htmlspecialchars($equipment); ?>"><?= htmlspecialchars($equipment); ?></option>
            <?php endforeach; ?>
            <option value="other">Other (Type Manually)</option>
        </select>
        <input type="text" id="custom_name" name="custom_name" placeholder="Enter item description" style="display:none;">

        <label for="model">Device Model:</label>
        <select name="model" id="model" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Device Model --</option>
        </select>
        <input type="text" id="custom_model" name="custom_model" placeholder="Enter model manually" style="display:none; margin-top:5px;">

        <label for="order_date">Order Date:</label>
        <input type="date" id="order_date" name="order_date" required style="text-align:center;">

        <label for="quantity_ordered">Quantity Ordered:</label>
        <input type="number" id="quantity_ordered" name="quantity_ordered" required style="text-align:center;">

        <label for="delivery_date">Delivery Date:</label>
        <input type="date" id="delivery_date" name="delivery_date" style="text-align:center;">

        <label for="quantity_delivered">Quantity Delivered:</label>
        <input type="number" id="quantity_delivered" name="quantity_delivered" style="text-align:center;">

        <label for="status">Status:</label>
        <select name="status" id="status" style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Status --</option>
            <option value="Complete" >Complete</option>
            <option value="Incomplete">Incomplete</option>
        </select>

        <label for="supplier">Supplier:</label>
        <select name="supplier" id="supplier" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align: center;">-- Select Supplier --</option>
            <option value="Amazon">Amazon</option>
            <option value="KGT">KGT</option>
            <option value="Missan">Missan</option>
            <option value="GetMax">GetMax</option>
            <option value="Modern Book Shop">Modern Book Shop</option>
            <option value="Kyoto">Kyoto</option>
            <option value="Reliable E&S Materials Trading LLC.">Reliable E&S Materials Trading LLC.</option>
            <option value="Miltec Rugged Computing Solutions LLC.">Miltec Rugged Computing Solutions LLC.</option>
            <option value="Trade Aid Computers LLC.">Trade Aid Computers LLC.</option>
            <option value="Jumaira Bookshop">Jumaira Bookshop</option>
            <option value="Sixth Gen Trading LLC.">Sixth Gen Trading LLC.</option>
        </select>

        <button type="submit" class="btn">Submit</button>
    </form>
</div>

<script>
function toggleCustomName() {
    const nameSelect = document.getElementById('name');
    const customNameInput = document.getElementById('custom_name');
    const modelDropdown = document.getElementById('model');
    const customModelInput = document.getElementById('custom_model');

    if (nameSelect.value === 'other') {
        customNameInput.style.display = 'block';
        customNameInput.required = true;

        modelDropdown.style.display = 'none';
        modelDropdown.required = false;

        customModelInput.style.display = 'block';
        customModelInput.required = true;
    } else {
        customNameInput.style.display = 'none';
        customNameInput.required = false;

        modelDropdown.style.display = 'block';
        modelDropdown.required = true;

        customModelInput.style.display = 'none';
        customModelInput.required = false;

        fetch('get_models.php?name=' + encodeURIComponent(nameSelect.value))
            .then(response => response.text())
            .then(data => {
                modelDropdown.innerHTML = data;
            });
    }
}
</script>

<script src="js/particles.min.js"></script>
<script>
particlesJS("particles-js", {
    "particles": {
        "number": { "value": 80, "density": { "enable": true, "value_area": 800 }},
        "color": { "value": "#000000" },
        "shape": { "type": "circle" },
        "opacity": { "value": 0.3 },
        "size": { "value": 3, "random": true },
        "line_linked": { "enable": true, "distance": 150, "color": "#000000", "opacity": 0.2, "width": 1 },
        "move": { "enable": true, "speed": 2 }
    },
    "interactivity": {
        "detect_on": "canvas",
        "events": { "onhover": { "enable": true, "mode": "grab" } }
    },
    "retina_detect": true
});
</script>

</body>
</html>