<?php
session_start();
include('db_connect.php'); // Ensure this connects using sqlsrv

// Fetch distinct model, type, color from SQL Server
$model_query = sqlsrv_query($conn, "SELECT DISTINCT model, type, color FROM itequip_inventory.toner_drum_inventory", [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = $_POST['model'];
    $type = $_POST['type'];
    $color = $_POST['color'];
    $checkout_date = $_POST['checkout_date'];
    $quantity_checked_out = intval($_POST['quantity_checked_out']);
    $requested_by = $_POST['requested_by'];

    // Check stock
    $stock_sql = "SELECT stock_quantity FROM itequip_inventory.toner_drum_inventory WHERE model = ? AND type = ? AND color = ?";
    $stock_params = [$model, $type, $color];
    $stock_stmt = sqlsrv_query($conn, $stock_sql, $stock_params);

    if ($stock_row = sqlsrv_fetch_array($stock_stmt, SQLSRV_FETCH_ASSOC)) {
        $current_stock = intval($stock_row['stock_quantity']);

        if ($current_stock >= $quantity_checked_out) {
            // Insert checkout
            $insert_sql = "INSERT INTO itequip_inventory.toner_drum_checkout (model, type, color, checkout_date, quantity_checked_out, requested_by) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_params = [$model, $type, $color, $checkout_date, $quantity_checked_out, $requested_by];
            $insert_stmt = sqlsrv_query($conn, $insert_sql, $insert_params);

            if ($insert_stmt) {
                // Update inventory
                $update_sql = "UPDATE itequip_inventory.toner_drum_inventory SET stock_quantity = stock_quantity - ? WHERE model = ? AND type = ? AND color = ?";
                $update_params = [$quantity_checked_out, $model, $type, $color];
                sqlsrv_query($conn, $update_sql, $update_params);

                // JavaScript alert for success
                echo "<script>
                        alert('Checkout recorded successfully!');
                        window.location.href = 'toner_drum_checkout.php'; // Redirect to checkout page
                      </script>";
            } else {
                // JavaScript alert for error
                echo "<script>
                        alert('Error recording checkout.');
                        window.location.href = 'toner_drum_checkout.php'; // Redirect to checkout page
                      </script>";
            }
        } else {
            // JavaScript alert for insufficient stock
            echo "<script>
                    alert('Not enough stock available for checkout.');
                    window.location.href = 'toner_drum_checkout.php'; // Redirect to checkout page
                  </script>";
        }
    } else {
        // JavaScript alert for item not found
        echo "<script>
                alert('Item not found in inventory.');
                window.location.href = 'toner_drum_checkout.php'; // Redirect to checkout page
              </script>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Printer Toner and Drum Stock Check-out | VERTIV GULF LLC ITMS</title>
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
            font-weight: 700;
            text-align: center;
            width: 100%;
            max-width: 450px;
            border-radius: 8px;
        }

        form {
            background: rgba(152, 199, 246, 0.6);
            margin: 20px auto;    /* Reduced margin */
            max-width: 450px;
            width: 100%;
        }

        label {
            font-weight: 600;
            margin-top: 10px;     /* Reduced vertical spacing */
            display: block;
            color: #2c3e50;
            font-size: 15px;      /* Smaller font */
        }

        input, select {
            width: 100%;
            padding: 8px;         /* Less padding */
            margin-top: 4px;
            margin-bottom: 12px;  /* Reduced space between fields */
            border-radius: 8px;   /* Optional: slightly smaller rounding */
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
            background-color:  rgb(4, 115, 15);
        }

        #model option,
        #color option,
        #type option,
        #requested_by option {
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
    <a href="stock_checkout_menu.php"><i class="material-icons">arrow_left</i> Stock Check-out Menu</a>
    <a href="it_asset_checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out IT Asset</a>
    <a href="checkout.php"><i class="material-icons">arrow_right</i> Stock Check-out IT Equipment</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">Printer Toner and Drum Stock Check-out</div>

<?php if (isset($successMsg)) echo "<p style='color: green;'>$successMsg</p>"; ?>
<?php if (isset($errorMsg)) echo "<p style='color: red;'>$errorMsg</p>"; ?>

<?php
$all_rows = [];
while ($row = sqlsrv_fetch_array($model_query, SQLSRV_FETCH_ASSOC)) {
    $all_rows[] = $row;
}
?>

<script>
    const inventoryData = <?php echo json_encode($all_rows); ?>;
</script>

<form method="POST">
    <label for="model">Model Name:</label>
    <select id="model" name="model" required style="text-align:center; text-align-last:center;">
        <option value="" style="text-align:center;">-- Select Model --</option>
        <?php
        $models = [];
        foreach ($all_rows as $row) {
            if (!in_array($row['model'], $models)) {
                echo "<option value='" . htmlspecialchars($row['model']) . "'>" . htmlspecialchars($row['model']) . "</option>";
                $models[] = $row['model'];
            }
        }
        ?>
    </select>

    <label for="color">Color:</label>
    <select id="color" name="color" required style="text-align:center; text-align-last:center;">
        <option value=""style="text-align:center;">-- Select Color --</option>
        <?php
        $colors = [];
        foreach ($all_rows as $row) {
            if (!in_array($row['color'], $colors)) {
                echo "<option value='" . htmlspecialchars($row['color']) . "'>" . htmlspecialchars($row['color']) . "</option>";
                $colors[] = $row['color'];
            }
        }
        ?>
    </select>

    <label for="type">Type:</label>
    <select id="type" name="type" required style="text-align:center; text-align-last:center;">
        <option value="" style="text-align:center;">-- Select Type: Toner or Drum or Cartridge --</option>
        <?php
        $types = [];
        foreach ($all_rows as $row) {
            if (!in_array($row['type'], $types)) {
                echo "<option value='" . htmlspecialchars($row['type']) . "'>" . htmlspecialchars($row['type']) . "</option>";
                $types[] = $row['type'];
            }
        }
        ?>
    </select>


        <label for="checkout_date">Checkout Date:</label>
        <input type="date" id="checkout_date" name="checkout_date" required style="text-align:center;">

        <label for="quantity_checked_out">Quantity Checked Out:</label>
        <input type="number" id="quantity_checked_out" name="quantity_checked_out" required style="text-align:center;">

        <label>Requested By:</label>
        <select name="requested_by" id="requested_by" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Requestor --</option>
            <option value="Accounts Main Building 1st Floor">Accounts Main Building 1st Floor</option>
            <option value="Al Hamra Porta Cabin">Al Hamra Porta Cabin</option>
            <option value="Camp Boss Office Accommodation Ground Floor">Camp Boss Office Accommodation Ground Floor</option>
            <option value="Engineering Main Building Ground Floor">Engineering Main Building Ground Floor</option>
            <option value="Estimation Main Building 1st Floor">Estimation Main Building 1st Floor</option>
            <option value="First Aid Room Ground Floor">First Aid Room Ground Floor</option>
            <option value="HR Main Building 1st Floor">HR Main Building 1st Floor</option>
            <option value="IMS 1st Floor">IMS 1st Floor</option>
            <option value="IMS 2nd Floor">IMS 2nd Floor</option>
            <option value="IMS QC Ground Floor">IMS QC Ground Floor</option>
            <option value="Information Technology Main Building 1st Floor">Information Technology Main Building 1st Floor</option>
            <option value="N31">N31</option>
            <option value="Purchasing Main Building Ground Floor">Purchasing Main Building Ground Floor</option>
            <option value="QC Office 1st Floor">QC Office 1st Floor</option>
            <option value="Reception Main Building Ground Floor">Reception Main Building Ground Floor</option>
            <option value="SMD / R&D 1st Floor">SMD / R&D 1st Floor</option>
            <option value="Store IMS Ground Floor">Store IMS Ground Floor</option>
            <option value="Store Main Building Ground Floor">Store Main Building Ground Floor</option>
            <option value="Switchgear Mezzanine 1st Floor">Switchgear Mezzanine 1st Floor</option>
            <option value="Switchgear Mezzanine Ground Floor">Switchgear Mezzanine Ground Floor</option>
            <option value="SV Room 1st Floor">SV Room 1st Floor</option>
            <option value="TR Testing Ground Floor">TR Testing Ground Floor</option>
        </select>

        <button type="submit" class="btn">Submit</button>
    </form>
</div>
        </div>
        </div>
        </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modelSelect = document.getElementById('model');
    const colorSelect = document.getElementById('color');
    const typeSelect = document.getElementById('type');

    modelSelect.addEventListener('change', function () {
        const selectedModel = this.value;
        

        // Clear and reset color and type dropdowns
        colorSelect.innerHTML = '<option value="">-- Select Color --</option>';
        typeSelect.innerHTML = '<option value="">-- Select Type: Toner or Drum or Cartridge --</option>';

        // Filter inventoryData for the selected model
        const filtered = inventoryData.filter(item => item.model === selectedModel);

        const colors = new Set();
        const types = new Set();

        filtered.forEach(item => {
            colors.add(item.color);
            types.add(item.type);
        });

        colors.forEach(color => {
            const option = document.createElement('option');
            option.value = color;
            option.textContent = color;
            colorSelect.appendChild(option);
        });

        types.forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            typeSelect.appendChild(option);
        });
    });
});
</script>

<!-- Particles.js library -->
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