<?php
include 'db_connect.php';

// Fetch unique equipment names and IDs for the dropdown
$name_query = "SELECT MIN(id) AS id, name FROM itequip_inventory.equipment GROUP BY name ORDER BY name ASC";
$name_result = sqlsrv_query($conn, $name_query);

// Fetch user names for dropdown
$user_query = "SELECT DISTINCT name FROM itequip_inventory.users_profile WHERE status = 'Active' ORDER BY name ASC";
$user_result = sqlsrv_query($conn, $user_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $equipment_id = $_POST['equipment_id'];
    $checkout_date = $_POST['checkout_date'];
    $quantity_checked_out = $_POST['quantity_checked_out'];
    $requested_by = $_POST['requested_by'];
    $service_now_request_ticket = $_POST['service_now_request_ticket'];
    $model = $_POST['model'];
    $reason_for_request = $_POST['reason_for_request'];

    // Get the equipment name based on the selected ID
    $sql_get_name = "SELECT name FROM itequip_inventory.equipment WHERE id = ?";
    $params_name = array($equipment_id);
    $stmt_name = sqlsrv_query($conn, $sql_get_name, $params_name);
    $name = "";
    if ($stmt_name && ($row = sqlsrv_fetch_array($stmt_name, SQLSRV_FETCH_ASSOC))) {
        $name = $row['name'];
    }

    // Check if model exists for that name
    $sql_check_model = "SELECT id, stock_quantity FROM itequip_inventory.equipment WHERE name = ? AND model = ?";
    $params_check = array($name, $model);
    $stmt_check = sqlsrv_query($conn, $sql_check_model, $params_check);

    if ($stmt_check && ($row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC))) {
        $equipment_id = $row['id'];
        $current_stock = $row['stock_quantity'];
    } else {
        $sql_insert_equipment = "INSERT INTO itequip_inventory.equipment (name, model, stock_quantity, status) OUTPUT INSERTED.id VALUES (?, ?, 0, 'Available')";
        $params_insert = array($name, $model);
        $stmt_insert = sqlsrv_prepare($conn, $sql_insert_equipment, $params_insert);
        if ($stmt_insert && sqlsrv_execute($stmt_insert)) {
            $result = sqlsrv_fetch_array($stmt_insert, SQLSRV_FETCH_ASSOC);
            if ($result) {
                $equipment_id = $result['id'];
                $current_stock = 0;
            } else {
                echo "<p class='error-message'>Failed to fetch inserted equipment ID.</p>";
                exit;
            }
        } else {
            echo "<p class='error-message'>Failed to create new equipment model entry.</p>";
            exit;
        }
    }

   // Check if the user has already checked out this item and the return status is NOT 'Returned' or 'Returned Faulty'
  $error_message = ""; // Initialize error message

if (!in_array(strtolower($requested_by), ['it', 'hr'])) { // Only apply policy if user is NOT 'IT'
    if (strtolower($name) === 'monitor') {
        // ✅ Enforce allowed quantity: 1 or 2
        if ($quantity_checked_out < 1 || $quantity_checked_out > 2) {
            $error_message = "You can only check out 1 to 2 Monitors per user.";
        } else {
            // ✅ Monitor: allow up to 2 active checkouts per user
            $sql_check_monitor = "
                SELECT COUNT(*) AS active_count 
                FROM itequip_inventory.checkouts 
                WHERE equipment_id = ? 
                AND requested_by = ? 
                AND (return_status IS NULL OR return_status NOT IN ('returned', 'returned_faulty'))
            ";
            $params_check_monitor = array($equipment_id, $requested_by);
            $stmt_check_monitor = sqlsrv_query($conn, $sql_check_monitor, $params_check_monitor);

            if ($stmt_check_monitor && $row = sqlsrv_fetch_array($stmt_check_monitor, SQLSRV_FETCH_ASSOC)) {
                if ($row['active_count'] >= 2) {
                    $error_message = "You have already checked out 2 Monitors of this user and it has not been returned yet.";
                }
            }
        }
    } else {
        // ✅ Non-Monitor items: quantity must be exactly 1
        if ($quantity_checked_out != 1) {
            $error_message = "Only 1 unit of this item can be checked out per user.";
        } else {
            // ✅ Check for duplicate unreturned item (by model)
            $sql_check_duplicate = "
                SELECT id 
                FROM itequip_inventory.checkouts 
                WHERE LOWER(model) = LOWER(?) 
                AND LOWER(requested_by) = LOWER(?) 
                AND (
                    return_status IS NULL 
                    OR LOWER(LTRIM(RTRIM(return_status))) NOT IN ('returned', 'returned_faulty')
                )
            ";
            $params_check_duplicate = array($model, $requested_by);
            $stmt_check_duplicate = sqlsrv_query($conn, $sql_check_duplicate, $params_check_duplicate);

            if ($stmt_check_duplicate && sqlsrv_fetch_array($stmt_check_duplicate, SQLSRV_FETCH_ASSOC)) {
                $error_message = "You have already checked out this item for this user and it has not been returned yet.";
            }
        }
    }
}

// ✅ If there's an error message, display it and stop further processing
if (!empty($error_message)) {
    echo "<script>
            alert('$error_message');
            window.location.href = 'checkout.php';
          </script>";
    exit;
}

// ✅ Proceed with checkout logic
if ($current_stock >= $quantity_checked_out) {
    $new_stock_quantity = $current_stock - $quantity_checked_out;

    // Update stock quantity
    $sql_update_stock = "UPDATE itequip_inventory.equipment SET stock_quantity = ? WHERE id = ?";
    $params_update = array($new_stock_quantity, $equipment_id);
    $stmt_update = sqlsrv_query($conn, $sql_update_stock, $params_update);

    if ($stmt_update) {
        // Record checkout
        $sql_checkout = "INSERT INTO itequip_inventory.checkouts 
                         (equipment_id, equipment_name, model, checkout_date, quantity_checked_out, requested_by, service_now_request_ticket, reason_for_request) 
                         OUTPUT INSERTED.id 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $params_checkout = array($equipment_id, $name, $model, $checkout_date, $quantity_checked_out, $requested_by, $service_now_request_ticket, $reason_for_request);
        $stmt_checkout = sqlsrv_prepare($conn, $sql_checkout, $params_checkout);

        if ($stmt_checkout && sqlsrv_execute($stmt_checkout)) {
            echo "<script>
                    alert('Stock checkout recorded successfully.');
                    window.location.href = 'checkout.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Failed to record stock checkout.');
                    window.location.href = 'checkout.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Failed to update stock.');
                window.location.href = 'checkout.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Not enough stock available for checkout.');
            window.location.href = 'checkout.php';
          </script>";
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
</head>
<style>
    /* Blinking error message */
    .error-message {
        color: red;
        font-weight: bold;
        animation: blink 1s linear infinite;
    }

    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }

    /* Existing styles */
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
        width: 40%;
        margin-top: 5px;
        margin-left: 110px;
        display: inline-block;
        transition: background 0.3s ease;
    }
    .btn:hover {
        background-color: rgb(4, 115, 15);
    }

    /* Make the options in the expanded dropdown left-aligned */
   #requested_by option,
   #equipment_id option,
   #model option,
   #reason_for_request option {
    text-align: left;
}

</style>
<body>

<div id="particles-js"></div>

<div class="sidebar">
    <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="stock_checkout_menu.php"><i class="material-icons">arrow_left</i> Stock Check-out Menu</a>
    <a href="it_asset_checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out IT Asset</a>
    <a href="toner_drum_checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out Printer Toner and Drum</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">IT Equipment Stock Check-out</div>

    <!-- Display error message if a duplicate checkout is detected -->
    <?php if (isset($error_message)) { ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php } ?>

    <form method="POST" action="checkout.php">
    <label for="requested_by">User:</label>
<select id="requested_by" name="requested_by" required style="text-align:center; text-align-last:center;">
    <option value="" style="text-align:center;">-- Select User --</option>
    <?php while ($row = sqlsrv_fetch_array($user_result, SQLSRV_FETCH_ASSOC)): ?>
        <option value="<?php echo htmlspecialchars($row['name']); ?>">
            <?php echo htmlspecialchars($row['name']); ?>
        </option>
    <?php endwhile; ?>
</select>

        <label for="equipment_id">Item Description:</label>
        <select id="equipment_id" name="equipment_id" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Equipment --</option>
            <?php while ($row = sqlsrv_fetch_array($name_result, SQLSRV_FETCH_ASSOC)): ?>
                <option value="<?php echo htmlspecialchars($row['id']); ?>">
                    <?php echo htmlspecialchars($row['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="model">Device Model:</label>
        <select id="model" name="model" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Device Model --</option>
        </select>

        <label for="reason_for_request">Reason for Request:</label>
        <select id="reason_for_request" name="reason_for_request" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Reason --</option>
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
document.getElementById("equipment_id").addEventListener("change", function () {
    var equipmentName = this.options[this.selectedIndex].text;

    if (equipmentName) {
        fetch("get_model_checkout.php?name=" + encodeURIComponent(equipmentName))
            .then(response => response.text())
            .then(data => {
                document.getElementById("model").innerHTML = data;
            })
            .catch(error => {
                console.error("Error fetching models:", error);
                document.getElementById("model").innerHTML = '<option value="">-- Error loading models --</option>';
            });
    } else {
        document.getElementById("model").innerHTML = '<option value="">-- Select Model --</option>';
    }
});
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
        "line_linked": { "enable": true, "color": "#000000", "opacity": 0.5 },
        "move": { "enable": true, "speed": 6, "direction": "random", "random": true }
    }
});
</script>

</body>
</html>
