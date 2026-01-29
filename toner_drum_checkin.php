<?php
session_start();
include('db_connect.php'); // SQL Server connection using sqlsrv

// Fetch model names from inventory
$model_query = "SELECT DISTINCT model FROM itequip_inventory.toner_drum_inventory ORDER BY model ASC";
$model_result = sqlsrv_query($conn, $model_query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = ($_POST['model'] === 'other') ? trim($_POST['custom_model']) : trim($_POST['model']);
    $color = trim($_POST['color']);
    $type = trim($_POST['type']);
    $order_date = $_POST['order_date'];
    $quantity_ordered = intval($_POST['quantity_ordered']);
    $delivery_date = $_POST['delivery_date'];
    $quantity_delivered = intval($_POST['quantity_delivered']);
    $status = $_POST['status']; // We'll still save it to the check-in table
    $supplier = trim($_POST['supplier']);

    // Proceed only if required fields are filled
    if ($model && $color && $type && $order_date && $quantity_ordered && $delivery_date && $quantity_delivered && $status && $supplier) {
        
        // Insert into check-in table (we always save this record)
        $insert_checkin_sql = "INSERT INTO itequip_inventory.toner_drum_checkin 
            (model, color, type, order_date, quantity_ordered, delivery_date, quantity_delivered, status, supplier) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$model, $color, $type, $order_date, $quantity_ordered, $delivery_date, $quantity_delivered, $status, $supplier];
        $stmt = sqlsrv_query($conn, $insert_checkin_sql, $params);

        if ($stmt) {
            // Check if the item exists in inventory
            $check_query = "SELECT stock_quantity FROM itequip_inventory.toner_drum_inventory WHERE model = ? AND color = ? AND type = ?";
            $check_stmt = sqlsrv_query($conn, $check_query, [$model, $color, $type]);

            if ($check_stmt && ($row = sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC))) {
                // Item exists, update stock
                $new_quantity = $row['stock_quantity'] + $quantity_delivered;

                $update_stmt = sqlsrv_query($conn,
                    "UPDATE itequip_inventory.toner_drum_inventory 
                     SET stock_quantity = ?, status = 'Available' 
                     WHERE model = ? AND color = ? AND type = ?",
                    [$new_quantity, $model, $color, $type]
                );

                if (!$update_stmt) {
                    die("Inventory update failed: " . print_r(sqlsrv_errors(), true));
                }

            } else {
                // Item does not exist, insert new
                $insert_inventory = sqlsrv_query($conn,
                    "INSERT INTO itequip_inventory.toner_drum_inventory 
                     (model, color, type, printer_name, printer_location, stock_quantity, status) 
                     VALUES (?, ?, ?, 'RAK', '', ?, 'Available')",
                    [$model, $color, $type, $quantity_delivered]
                );

                if (!$insert_inventory) {
                    die("Inventory insert failed: " . print_r(sqlsrv_errors(), true));
                }
            }

            echo "<script>
                alert('Printer Toner/Drum Stock Check-in successful!');
                window.location.href = 'toner_drum_checkin.php';
            </script>";
        } else {
            echo "<p class='error-message'>Error inserting check-in record: " . print_r(sqlsrv_errors(), true) . "</p>";
        }
    } else {
        echo "<p class='error-message'>Please fill in all required fields.</p>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Printer Toner and Drum Stock Check-In | VERTIV GULF LLC ITMS ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
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

        .success-message {
            color: green;
            font-weight: bold;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        #model option,
        #color option,
        #type option,
        #status option,
        #supplier option {
    text-align: left;
        }

    </style>
    <script>
        function toggleCustomModel() {
            const modelDropdown = document.getElementById("model");
            const customInput = document.getElementById("custom_model");

            if (modelDropdown.value === "other") {
                customInput.style.display = "block";
                customInput.required = true;
            } else {
                customInput.style.display = "none";
                customInput.required = false;
            }
        }
    </script>
</head>
<body>

<div id="particles-js"></div>

<div class="sidebar">
  <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
  <hr class="sidebar-divider">
  <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
  <a href="it_asset_checkin.php"><i class="material-icons">arrow_right</i>Stock Check-in IT Asset</a>
  <a href="checkin.php"><i class="material-icons">arrow_right</i> Stock Check-in IT Equipment</a>
  <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">Printer Toner and Drum Stock Check-in</div>

    <form method="POST">
        <label for="model">Model Name:</label>
        <select name="model" id="model" onchange="toggleCustomModel()" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Model --</option>
            <?php while ($row = sqlsrv_fetch_array($model_result, SQLSRV_FETCH_ASSOC)): ?>
                <option value="<?= htmlspecialchars($row['model']) ?>"><?= htmlspecialchars($row['model']) ?></option>
            <?php endwhile; ?>
            <option value="other">Other (Type Manually)</option>
        </select>
        <input type="text" id="custom_model" name="custom_model" placeholder="Enter model name" style="display:none; margin-top: 10px;">

        <label for="color">Color:</label>
        <select name="color" id="color" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Color --</option>
            <option value="Black">Black</option>
            <option value="Cyan">Cyan</option>
            <option value="Yellow">Yellow</option>
            <option value="Magenta">Magenta</option>
            <option value="Tri-Color">Tri-Color</option>
        </select>

        <label for="type">Type:</label>
        <select id="type" name="type" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Type: Toner or Drum or Cartridge --</option>
            <option value="Toner">Toner</option>
            <option value="Drum">Drum</option>
            <option value="Cartridge">Cartridge</option>
        </select>

        <label for="order_date">Order Date:</label>
        <input type="date" id="order_date" name="order_date" required style="text-align: center;">

        <label for="quantity_ordered">Quantity Ordered:</label>
        <input type="number" id="quantity_ordered" name="quantity_ordered" required style="text-align: center;">

        <label for="delivery_date">Delivery Date:</label>
        <input type="date" id="delivery_date" name="delivery_date" required style="text-align: center;">

        <label for="quantity_delivered">Quantity Delivered:</label>
        <input type="number" id="quantity_delivered" name="quantity_delivered" required style="text-align: center;">

        <label for="status">Status:</label>
        <select id="status" name="status" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Status --</option>
            <option value="Complete">Complete</option>
            <option value="Incomplete">Incomplete</option>
        </select>

        <label for="supplier">Supplier:</label>
        <select name="supplier" id="supplier" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Supplier --</option>
            <option value="IMAGE">IMAGE</option>
            <option value="KGT">KGT</option>
            <option value="Modern Book Shop">Modern Book Shop</option>
        </select>

        <button type="submit" class="btn">Submit</button>
    </form>
</div>

<script src="js/particles.min.js"></script>
<script>
  particlesJS("particles-js", {
    "particles": {
      "number": {
        "value": 80,
        "density": { "enable": true, "value_area": 800 }
      },
      "color": { "value": "#000000" },
      "shape": {
        "type": "circle",
        "stroke": { "width": 0, "color": "#000000" },
        "polygon": { "nb_sides": 5 }
      },
      "opacity": {
        "value": 0.3,
        "random": false,
        "anim": { "enable": false }
      },
      "size": {
        "value": 3,
        "random": true,
        "anim": { "enable": false }
      },
      "line_linked": {
        "enable": true,
        "distance": 150,
        "color": "#000000",
        "opacity": 0.2,
        "width": 1
      },
      "move": {
        "enable": true,
        "speed": 2,
        "direction": "none",
        "random": false,
        "straight": false,
        "out_mode": "out",
        "bounce": false
      }
    },
    "interactivity": {
      "detect_on": "canvas",
      "events": {
        "onhover": { "enable": true, "mode": "grab" },
        "onclick": { "enable": false },
        "resize": true
      },
      "modes": {
        "grab": { "distance": 140, "line_linked": { "opacity": 0.4 } }
      }
    },
    "retina_detect": true
  });
</script>
</body>
</html>
