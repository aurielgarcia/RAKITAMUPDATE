<?php
session_start();
include 'db_connect.php';

$name_query = "SELECT DISTINCT name FROM itequip_inventory.equipment ORDER BY name ASC";
$name_result = sqlsrv_query($conn, $name_query);

$user_query = "SELECT DISTINCT name FROM itequip_inventory.users_profile WHERE status = 'Active' ORDER BY name ASC";
$user_result = sqlsrv_query($conn, $user_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $equipment_name = $_POST['equipment_name'];
    $model = $_POST['model'];
    $site_location = $_POST['site_location'];
    $storage_location = $_POST['storage_location'];
    $checkout_date = $_POST['checkout_date'];
    $quantity_checked_out = (int)$_POST['quantity_checked_out'];
    $requested_by = $_POST['requested_by'];
    $service_now_request_ticket = $_POST['service_now_request_ticket'];
    $reason_for_request = $_POST['reason_for_request'];
    $deployed_by = $_SESSION['name'] ?? 'System';

    $sql_check = "SELECT id, stock_quantity FROM itequip_inventory.equipment 
                  WHERE name = ? AND model = ? AND site_location = ? AND storage_location = ?";
    $params_check = array($equipment_name, $model, $site_location, $storage_location);
    $stmt_check = sqlsrv_query($conn, $sql_check, $params_check);

    $error_message = "";

    if ($stmt_check && ($row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC))) {
        $equipment_id = $row['id'];
        $current_stock = $row['stock_quantity'];

        if (!in_array(strtolower($requested_by), ['it', 'hr'])) {
            if (strtolower($equipment_name) === 'monitor') {
                if ($quantity_checked_out < 1 || $quantity_checked_out > 2) {
                    $error_message = "You can only check out 1 to 2 Monitors per user.";
                } else {
                    $sql_mon_count = "SELECT COUNT(*) AS active_count FROM itequip_inventory.checkouts 
                                     WHERE equipment_name = ? AND requested_by = ? 
                                     AND (return_status IS NULL OR return_status NOT IN ('returned', 'returned_faulty'))";
                    $stmt_mon = sqlsrv_query($conn, $sql_mon_count, array($equipment_name, $requested_by));
                    if ($stmt_mon && $r = sqlsrv_fetch_array($stmt_mon, SQLSRV_FETCH_ASSOC)) {
                        if ($r['active_count'] >= 2) {
                            $error_message = "This user already has 2 active monitors checked out.";
                        }
                    }
                }
            } else {
                if ($quantity_checked_out != 1) {
                    $error_message = "Only 1 unit of this item can be checked out per user.";
                } else {
                    $sql_dup = "SELECT id FROM itequip_inventory.checkouts 
                                WHERE LOWER(model) = LOWER(?) AND LOWER(requested_by) = LOWER(?) 
                                AND (return_status IS NULL OR return_status NOT IN ('returned', 'returned_faulty'))";
                    $stmt_dup = sqlsrv_query($conn, $sql_dup, array($model, $requested_by));
                    if ($stmt_dup && sqlsrv_fetch_array($stmt_dup, SQLSRV_FETCH_ASSOC)) {
                        $error_message = "User already has an active checkout for this model.";
                    }
                }
            }
        }

        if (!empty($error_message)) {
            echo "<script>alert('$error_message'); window.history.back();</script>";
            exit;
        }

        if ($current_stock >= $quantity_checked_out) {
            $new_stock = $current_stock - $quantity_checked_out;
            $sql_update = "UPDATE itequip_inventory.equipment SET stock_quantity = ? WHERE id = ?";
            sqlsrv_query($conn, $sql_update, array($new_stock, $equipment_id));

            $sql_ins = "INSERT INTO itequip_inventory.checkouts 
                        (equipment_id, equipment_name, model, checkout_date, quantity_checked_out, requested_by, service_now_request_ticket, deployed_by, reason_for_request, site_location, storage_location) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params_ins = array($equipment_id, $equipment_name, $model, $checkout_date, $quantity_checked_out, $requested_by, $service_now_request_ticket, $deployed_by, $reason_for_request, $site_location, $storage_location);
            
            $stmt_ins = sqlsrv_query($conn, $sql_ins, $params_ins);

            if ($stmt_ins) {
                echo "<script>alert('Stock checkout recorded successfully.'); window.location.href = 'checkout.php';</script>";
            } else {
                $errors = sqlsrv_errors();
                $error_msg = "Database Error: " . ($errors ? $errors[0]['message'] : "Unknown error.");
                echo "<script>alert(" . json_encode($error_msg) . "); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Not enough stock available in the selected location.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('The specified item/model/location combination does not exist in inventory.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Equipment Stock Check-out | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
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
            font-weight: 700;
            text-align: center;
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
        }
        form {
            background: rgba(152, 199, 246, 0.6);
            margin: 20px auto;
            max-width: 450px;
            width: 100%;
            padding: 20px;
            border-radius: 12px;
        }
        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
            color: #2c3e50;
            font-size: 14px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            margin-bottom: 12px;
            border-radius: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
        }
        .btn {
            background-color: rgb(91, 160, 13);
            color: white;
            font-weight: bold;
            padding: 12px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background-color: rgb(4, 115, 15);
        }
    </style>
</head>
<body>

<div id="particles-js"></div>

<div class="sidebar">
    <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="stock_checkout_menu.php"><i class="material-icons">arrow_left</i> Stock Check-out Menu</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">IT Equipment Stock Check-out</div>

    <form method="POST" action="checkout.php">
        <label for="requested_by">User:</label>
        <select id="requested_by" name="requested_by" required style="text-align:center;">
            <option value="">-- Select User --</option>
            <?php while ($row = sqlsrv_fetch_array($user_result, SQLSRV_FETCH_ASSOC)): ?>
                <option value="<?= htmlspecialchars($row['name']); ?>"><?= htmlspecialchars($row['name']); ?></option>
            <?php endwhile; ?>
        </select>

        <label for="equipment_name">Item Description:</label>
        <select id="equipment_name" name="equipment_name" required style="text-align:center;" onchange="fetchModels()">
            <option value="">-- Select Equipment --</option>
            <?php while ($row = sqlsrv_fetch_array($name_result, SQLSRV_FETCH_ASSOC)): ?>
                <option value="<?= htmlspecialchars($row['name']); ?>"><?= htmlspecialchars($row['name']); ?></option>
            <?php endwhile; ?>
        </select>

        <label for="model">Device Model:</label>
        <select id="model" name="model" required style="text-align:center;">
            <option value="">-- Select Device Model --</option>
        </select>

        <label for="site_location">Site Location:</label>
        <select name="site_location" id="site_location" onchange="updateStorageLocations()" required style="text-align:center;">
            <option value="">-- Select Site Location --</option>
            <option value="Al Ghail">Al Ghail</option>
            <option value="Al Hamra">Al Hamra</option>
        </select>

        <label for="storage_location">Storage Location:</label>
        <select name="storage_location" id="storage_location" required style="text-align:center;">
            <option value="">-- Select Site First --</option>
        </select>

        <label for="reason_for_request">Reason for Request:</label>
        <select id="reason_for_request" name="reason_for_request" required style="text-align:center;">
            <option value="">-- Select Reason --</option>
            <option value="New Hire">New Hire</option>
            <option value="Return Faulty">Return Faulty</option>
            <option value="Replacement - Lost/Stolen Device">Replacement - Lost/Stolen Device</option>
        </select>

        <label for="checkout_date">Checkout Date:</label>
        <input type="date" id="checkout_date" name="checkout_date" required style="text-align: center;">

        <label for="quantity_checked_out">Quantity Checked Out:</label>
        <input type="number" id="quantity_checked_out" name="quantity_checked_out" min="1" required style="text-align:center;">

        <label for="service_now_request_ticket">ServiceNow Request Ticket:</label>
        <input type="text" id="service_now_request_ticket" name="service_now_request_ticket" required style="text-align:center;">

        <button type="submit" class="btn">Submit for Releasing</button>
    </form>
</div>

<script>
function fetchModels() {
    const equipmentName = document.getElementById("equipment_name").value;
    const modelDropdown = document.getElementById("model");
    if (equipmentName) {
        fetch("get_model_checkout.php?name=" + encodeURIComponent(equipmentName))
            .then(response => response.text())
            .then(data => { modelDropdown.innerHTML = data; })
            .catch(error => { modelDropdown.innerHTML = '<option value="">-- Error --</option>'; });
    } else {
        modelDropdown.innerHTML = '<option value="">-- Select Model --</option>';
    }
}

function updateStorageLocations() {
    const site = document.getElementById('site_location').value;
    const storageDropdown = document.getElementById('storage_location');
    storageDropdown.innerHTML = '<option value="">-- Select Storage Location --</option>';

    const ghailLocations = ["I.T. Department Main Building", "Stock Room Main Building", "Stock Room IMS Building"];
    const hamraLocations = ["I.T. Department Main Building", "Storage Room"];

    let options = site === "Al Ghail" ? ghailLocations : (site === "Al Hamra" ? hamraLocations : []);
    options.forEach(loc => {
        const opt = document.createElement('option');
        opt.value = loc;
        opt.textContent = loc;
        storageDropdown.appendChild(opt);
    });
}
</script>

<script src="js/particles.min.js"></script>
<script>
particlesJS("particles-js", {
    "particles": {
        "number": { "value": 60, "density": { "enable": true, "value_area": 800 }},
        "color": { "value": "#000000" },
        "shape": { "type": "circle" },
        "opacity": { "value": 0.2 },
        "size": { "value": 3, "random": true },
        "line_linked": { "enable": true, "distance": 150, "color": "#000000", "opacity": 0.1, "width": 1 },
        "move": { "enable": true, "speed": 1 }
    }
});
</script>

</body>
</html>