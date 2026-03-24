<?php
session_start();
include('db_connect.php');

$model_query = sqlsrv_query($conn, "SELECT DISTINCT model, type, color, site_location, storage_location, stock_quantity FROM itequip_inventory.toner_drum_inventory", [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = $_POST['model'];
    $type = $_POST['type'];
    $color = $_POST['color'];
    $site_location = $_POST['site_location'];
    $storage_location = $_POST['storage_location'];
    $checkout_date = $_POST['checkout_date'];
    $quantity_checked_out = intval($_POST['quantity_checked_out']);
    $deployed_by = $_SESSION['name'] ?? 'System';
    $requested_by = $_POST['requested_by'];

    $stock_sql = "SELECT stock_quantity FROM itequip_inventory.toner_drum_inventory 
                  WHERE model = ? AND type = ? AND color = ? AND site_location = ? AND storage_location = ?";
    $stock_params = [$model, $type, $color, $site_location, $storage_location];
    $stock_stmt = sqlsrv_query($conn, $stock_sql, $stock_params);

    if ($stock_row = sqlsrv_fetch_array($stock_stmt, SQLSRV_FETCH_ASSOC)) {
        $current_stock = intval($stock_row['stock_quantity']);

        if ($current_stock >= $quantity_checked_out) {
            $insert_sql = "INSERT INTO itequip_inventory.toner_drum_checkout 
                (model, type, color, checkout_date, quantity_checked_out, deployed_by, requested_by, site_location, storage_location) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_params = [$model, $type, $color, $checkout_date, $quantity_checked_out, $deployed_by, $requested_by, $site_location, $storage_location];
            $insert_stmt = sqlsrv_query($conn, $insert_sql, $insert_params);

            if ($insert_stmt) {
                $update_sql = "UPDATE itequip_inventory.toner_drum_inventory 
                               SET stock_quantity = stock_quantity - ? 
                               WHERE model = ? AND type = ? AND color = ? AND site_location = ? AND storage_location = ?";
                $update_params = [$quantity_checked_out, $model, $type, $color, $site_location, $storage_location];
                sqlsrv_query($conn, $update_sql, $update_params);

                echo "<script>
                        alert('Checkout recorded successfully!');
                        window.location.href = 'toner_drum_checkout.php';
                      </script>";
            } else {
                echo "<script>alert('Error recording checkout.');</script>";
            }
        } else {
            echo "<script>alert('Not enough stock available at the selected location.');</script>";
        }
    } else {
        echo "<script>alert('Item not found in inventory for the selected location.');</script>";
    }
}

$all_rows = [];
while ($row = sqlsrv_fetch_array($model_query, SQLSRV_FETCH_ASSOC)) {
    $all_rows[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Printer Toner and Drum Stock Check-out | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; }
        #particles-js { position: absolute; width: 100%; height: 100%; background-color: #ffffff; z-index: 0; }
        .main-content { margin-left: 200px; padding: 20px; position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; }
        .tab-header { background: rgba(44, 62, 80, 0.6); color: white; padding: 20px 30px; font-size: 18px; font-weight: 700; text-align: center; width: 100%; max-width: 450px; border-radius: 8px; }
        form { background: rgba(152, 199, 246, 0.6); margin: 20px auto; max-width: 450px; width: 100%; padding: 20px; border-radius: 12px; }
        label { font-weight: 600; margin-top: 10px; display: block; color: #2c3e50; font-size: 15px; }
        input, select { width: 100%; padding: 8px; margin-top: 4px; margin-bottom: 12px; border-radius: 8px; font-size: 14px; border: 1px solid #ccc; }
        .btn { background-color: rgb(91, 160, 13); color: white; font-weight: bold; padding: 12px; border: none; border-radius: 10px; cursor: pointer; width: 100%; transition: background 0.3s ease; }
        .btn:hover { background-color: rgb(4, 115, 15); }
    </style>
</head>
<body>

<div id="particles-js"></div>

<div class="sidebar">
  <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
  <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="stock_checkout_menu.php"><i class="material-icons">arrow_left</i> Stock Check-out Menu</a>
    <a href="it_asset_checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out IT Asset</a>
    <a href="checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out IT Equipment</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">Printer Toner and Drum Stock Check-out</div>

    <form method="POST">
        <label for="site_location">Site Location:</label>
        <select name="site_location" id="site_location" required onchange="updateDynamicFields()" style="text-align:center;">
            <option value="">-- Select Site --</option>
            <option value="Al Ghail">Al Ghail</option>
            <option value="Al Hamra">Al Hamra</option>
        </select>

        <label for="storage_location">Storage Location:</label>
        <select name="storage_location" id="storage_location" required style="text-align:center;">
            <option value="">-- Select Site First --</option>
        </select>

        <label for="requested_by">Requested By (Office):</label>
        <select name="requested_by" id="requested_by" required style="text-align:center;">
            <option value="">-- Select Site First --</option>
        </select>

        <label for="model">Model Name:</label>
        <select id="model" name="model" required style="text-align:center;">
            <option value="">-- Select Model --</option>
            <?php
            $models = array_unique(array_column($all_rows, 'model'));
            foreach ($models as $m) echo "<option value='".htmlspecialchars($m)."'>".htmlspecialchars($m)."</option>";
            ?>
        </select>

        <label for="color">Color:</label>
        <select id="color" name="color" required style="text-align:center;">
            <option value="">-- Select Color --</option>
        </select>

        <label for="type">Type:</label>
        <select id="type" name="type" required style="text-align:center;">
            <option value="">-- Select Type --</option>
        </select>

        <label for="checkout_date">Checkout Date:</label>
        <input type="date" id="checkout_date" name="checkout_date" required style="text-align:center;">

        <label for="quantity_checked_out">Quantity Checked Out:</label>
        <input type="number" id="quantity_checked_out" name="quantity_checked_out" required style="text-align:center;">

        <button type="submit" class="btn">Submit Checkout</button>
    </form>
</div>

<script>
    const inventoryData = <?php echo json_encode($all_rows); ?>;

    const locationsData = {
        "Al Ghail": {
            "storage": ["I.T. Department Main Building", "Stock Room Main Building", "Stock Room IMS Building"],
            "requested": [
                "Accounts Main Building 1st Floor", "Al Hamra Porta Cabin", "Camp Boss Office Accommodation Ground Floor",
                "Engineering Main Building Ground Floor", "Estimation Main Building 1st Floor", "First Aid Room Ground Floor",
                "HR Main Building 1st Floor", "IMS 1st Floor", "IMS 2nd Floor", "IMS QC Ground Floor",
                "Information Technology Main Building 1st Floor", "N31", "Purchasing Main Building Ground Floor",
                "QC Office 1st Floor", "Reception Main Building Ground Floor", "SMD / R&D 1st Floor",
                "Store IMS Ground Floor", "Store Main Building Ground Floor", "Switchgear Mezzanine 1st Floor",
                "Switchgear Mezzanine Ground Floor", "SV Room 1st Floor", "TR Testing Ground Floor"
            ]
        },
        "Al Hamra": {
            "storage": ["I.T. Department Main Building", "Storage Room"],
            "requested": ["Ground Floor Office"]
        }
    };

    function updateDynamicFields() {
        const site = document.getElementById('site_location').value;
        const storageDropdown = document.getElementById('storage_location');
        const requestedDropdown = document.getElementById('requested_by');

        storageDropdown.innerHTML = '<option value="">-- Select Storage --</option>';
        requestedDropdown.innerHTML = '<option value="">-- Select Requestor --</option>';

        if (locationsData[site]) {
            locationsData[site].storage.forEach(loc => {
                let opt = new Option(loc, loc);
                storageDropdown.add(opt);
            });
            locationsData[site].requested.forEach(req => {
                let opt = new Option(req, req);
                requestedDropdown.add(opt);
            });
        }
    }

    document.getElementById('model').addEventListener('change', function () {
        const selectedModel = this.value;
        const colorSelect = document.getElementById('color');
        const typeSelect = document.getElementById('type');

        colorSelect.innerHTML = '<option value="">-- Select Color --</option>';
        typeSelect.innerHTML = '<option value="">-- Select Type --</option>';

        const filtered = inventoryData.filter(item => item.model === selectedModel);
        const colors = [...new Set(filtered.map(item => item.color))];
        const types = [...new Set(filtered.map(item => item.type))];

        colors.forEach(c => colorSelect.add(new Option(c, c)));
        types.forEach(t => typeSelect.add(new Option(t, t)));
    });
</script>

<script src="js/particles.min.js"></script>
<script>
  particlesJS("particles-js", {
    "particles": {
      "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
      "color": { "value": "#000000" },
      "shape": { "type": "circle" },
      "opacity": { "value": 0.3 },
      "size": { "value": 3, "random": true },
      "line_linked": { "enable": true, "distance": 150, "color": "#000000", "opacity": 0.2, "width": 1 },
      "move": { "enable": true, "speed": 2 }
    }
  });
</script>
</body>
</html>