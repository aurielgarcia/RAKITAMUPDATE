<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

// Get user names from users_profile table
$userOptions = "";
$userQuery = "SELECT name FROM itequip_inventory.users_profile ORDER BY name ASC";
$userStmt = sqlsrv_query($conn, $userQuery);

if ($userStmt) {
    while ($row = sqlsrv_fetch_array($userStmt, SQLSRV_FETCH_ASSOC)) {
        $fullName = htmlspecialchars($row['name']);
        $userOptions .= "<option value=\"$fullName\">$fullName</option>";
    }
} else {
    echo "Error fetching users: ";
    print_r(sqlsrv_errors());
}



// Fetch unique serial numbers (run always so dropdown populates)
$sql = "SELECT serial_number FROM dbo.it_asset_inventory 
        WHERE install_status IN ('In Stock - New', 'In Stock - Spare') 
        ORDER BY serial_number ASC";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serial_number = $_POST['serial_number'];
    $item_description = $_POST['item_description'];
    $device_model = $_POST['device_model'];
    $device_type = $_POST['device_type'];
    $install_status = $_POST['install_status'];
    $assigned_to = $_POST['assigned_to'];
    $service_now_request_ticket = $_POST['service_now_request_ticket'];
    $deployed_by = $_POST['deployed_by'];
    $deployed_date_str = $_POST['deployed_date'];
    $device_storage_location = $_POST['device_storage_location'];

    // Validate and format deployed_date
    $deployed_date = null;
    if (!empty($deployed_date_str)) {
        $dateObj = date_create($deployed_date_str);
        if ($dateObj === false) {
            echo "<script>alert('Invalid deployed date format.'); window.location.href = 'it_asset_checkout.php';</script>";
            exit;
        }
        $deployed_date = $dateObj->format('Y-m-d H:i:s');
    }


    // Update dbo.it_asset_inventory record
    $sql_update = "UPDATE dbo.it_asset_inventory SET
    item_description = ?,
    device_model = ?,
    device_type = ?,
    service_now_request_ticket = ?,
    device_storage_location = ?,
    install_status = ?,
    assigned_to = ?,
    deployed_by = ?,
    deployed_date = ?
    WHERE serial_number = ?";
$params_update = array(
    $item_description,
    $device_model,
    $device_type,
    $service_now_request_ticket,         // make sure this variable is named correctly in PHP
    $device_storage_location,           // same here
    $install_status,
    $assigned_to,
    $deployed_by,
    $deployed_date,
    $serial_number
);

    $stmt_update = sqlsrv_prepare($conn, $sql_update, $params_update);
    if ($stmt_update === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $exec_update = sqlsrv_execute($stmt_update);
    if ($exec_update === false) {
        die("Update execution error: " . print_r(sqlsrv_errors(), true));
    } else {
        echo "<script>alert('Asset checkout recorded successfully.'); window.location.href = 'it_asset_checkout.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Asset Checkout | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<style>
    /* Your styles here */
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
        padding: 15px 20px;
        font-size: 20px;
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
        font-weight: 700;
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

     #assigned_to option {
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
    <a href="checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out IT Equipment</a>
    <a href="toner_drum_checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out Printer Toner and Drum</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">IT Asset Stock Check-out</div>
    <form method="POST" action="it_asset_checkout.php">
       <select name="serial_number" id="serial_number" required style="text-align:center;">
    <option value="">-- Select Sevice Tag --</option>
    <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
        <option value="<?= htmlspecialchars($row['serial_number']) ?>">
            <?= htmlspecialchars($row['serial_number']) ?>
        </option>
    <?php endwhile; ?>
</select>

        <label for="item_description">Item Description</label>
        <input type="text" name="item_description" id="item_description" readonly style="text-align:center;">

        <label for="device_model">Device Model</label>
        <input type="text" name="device_model" id="device_model" readonly style="text-align:center;">

        <label for="device_type">Device Type</label>
        <input type="text" name="device_type" id="device_type" readonly style="text-align:center;">

        <label for="install_status">Install Status</label>
        <select name="install_status" id="install_status" required style="text-align:center;">
            <option value="">-- Select Status --</option>
            <option value="Installed">Installed</option>
            <option value="Retired">Retired</option>
        </select>

        <label for="assigned_to">Assigned To</label>
        <select name="assigned_to" id="assigned_to" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select a user --</option>
            <?php echo $userOptions; ?>
        </select>

        <label for="service_now_request_ticket">ServiceNow Request Ticket</label>
        <input type="text" name="service_now_request_ticket" id="service_now_request_ticket" required style="text-align:center;">

        <label for="deployed_by">Deployed By</label>
        <select name="deployed_by" id="deployed_by" required style="text-align:center;">
            <option value="">-- Select User --</option>
            <option value="Ralph Carlo Sevilla">Ralph Carlo Sevilla</option>
            <option value="Julieben Labrada">Julieben Labrada</option>
            <option value="Gilbert Bautista">Gilbert Bautista</option>
            <option value="Auriel Garcia">Auriel Garcia</option>
            <option value="Therrence SeracCatalan">Therrence SeracCatalan</option>
        </select>

        <label for="deployed_date">Deployed Date</label>
        <input type="date" name="deployed_date" id="deployed_date" style="text-align:center;">

        <label for="device_storage_location">Device Storage Location</label>
        <select name="device_storage_location" id="device_storage_location" required style="text-align:center;">
            <option value="">-- Select Storage Location --</option>
            <option value="Released to the User">Released to the User</option>
            <option value="I.T. Department Main Building">I.T. Department Main Building</option>
            <option value="Stock Room Main Building">Stock Room Main Building</option>
        </select>

        <button type="submit" class="btn">Submit</button>
    </form>
</div>

<script src="particles.min.js"></script>
<script src="app.js"></script>

<script>
document.getElementById('serial_number').addEventListener('change', function() {
    var serialNumber = this.value;
    if (!serialNumber) {
        // Clear fields if no selection
        document.getElementById('item_description').value = '';
        document.getElementById('device_model').value = '';
        document.getElementById('device_type').value = '';
        return;
    }

    fetch('get_asset_info.php?serial_number=' + encodeURIComponent(serialNumber))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            document.getElementById('item_description').value = data.item_description || '';
            document.getElementById('device_model').value = data.device_model || '';
            document.getElementById('device_type').value = data.device_type || '';
        })
        .catch(err => {
            console.error('Error fetching asset info:', err);
        });
});
</script>

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
</body>
</html>
