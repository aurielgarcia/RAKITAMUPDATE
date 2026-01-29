<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_description = $_POST['item_description'];
    $device_model     = $_POST['device_model'];
    $serial_number        = $_POST['serial_number'];
    $processor        = $_POST['device_processor'];
    $storage          = $_POST['device_storage'];
    $ram              = $_POST['device_ram'];
    $gpu              = $_POST['device_gpu'];
    $device_type      = $_POST['device_type'];
    $install_status      = $_POST['install_status'];
    $purchased_date     = $_POST['purchased_date'];
    $device_storage_location     = $_POST['device_storage_location'];

    $insert_sql = "INSERT INTO dbo.it_asset_inventory 
        (item_description, device_model, serial_number, device_processor, device_storage, device_ram, device_gpu, device_type, install_status,purchased_date, device_storage_location)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $params = [$item_description, $device_model, $serial_number, $processor, $storage, $ram, $gpu, $device_type, $install_status,$purchased_date, $device_storage_location];
    $stmt = sqlsrv_query($conn, $insert_sql, $params);

    if ($stmt) {
        echo "<script>
            alert('IT Asset Check-in successful!');
            window.location.href = 'it_asset_checkin.php';
        </script>";
    } else {
        $errors = sqlsrv_errors();
        echo "<p class='error-message'>Error inserting record: " . $errors[0]['message'] . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Asset Stock Check-in | VERTIV GULF LLC ITMS ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="bootstrap.min.css">
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
            background: rgba(152, 199, 246, 0.6);;
            margin: 20px auto;
            max-width: 500px;
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
            width: 30%;
            margin: 20px auto 0;
            display: block;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background-color: rgb(4, 115, 15);
        }

        #device_model option,
        #device_storage option,
        #device_ram option,
        #device_gpu option,
        #device_type option,
        #install_status option,
        #device_storage_location option {
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
    <a href="checkin.php"><i class="material-icons">arrow_right</i>Stock Check-in IT Equipment</a>
    <a href="toner_drum_checkin.php"><i class="material-icons">arrow_right</i>Stock Check-in Printer Toner and Drum</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">IT Asset Stock Check-in</div>

    <form method="POST" action="it_asset_checkin.php">
        <label for="item_description">Item Description:</label>
        <select name="item_description" id="item_description" required style="text-align:center;">
            <option value="">-- Select Type --</option>
            <option value="All-In-One">All-In-One</option>
            <option value="Desktop">Desktop</option>
            <option value="Laptop">Laptop</option>
        </select>


        <label for="device_model">Device Model:</label>
        <select name="device_model" id="device_model" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Device Model --</option>
            <option value="Dell Optiplex AIO 7400, 35W">Dell Optiplex AIO 7400, 35W</option>
            <option value="Dell Optiplex AIO 7410, 35W">Dell Optiplex AIO 7410, 35W</option>
            <option value="Dell Optiplex AIO 7410, 65W">Dell Optiplex AIO 7410, 65W</option>
            <option value="Dell Optiplex AIO 7420, 35W">Dell Optiplex AIO 7420, 35W</option>
            <option value="Dell Optiplex 7000">Dell Optiplex 7000</option>
            <option value="Dell Latitude 3510">Dell Latitude 3510</option>
            <option value="Dell Latitude 5420">Dell Latitude 5420</option>
            <option value="Dell Latitude 5430">Dell Latitude 5430</option>
            <option value="Dell Latitude 5440">Dell Latitude 5440</option>
            <option value="Dell Latitude 5450">Dell Latitude 5450</option>
            <option value="Dell Latitude 5540">Dell Latitude 5540</option>
            <option value="Dell Latitude 7420">Dell Latitude 7420</option>
            <option value="Dell Latitude 7450">Dell Latitude 7450</option>
            <option value="Dell Precision 5570">Dell Precision 5570</option>
            <option value="Dell Precision 5680">Dell Precision 5680</option>
            <option value="Dell Precision 5690">Dell Precision 5690</option>
            <option value="Dell Precision 7670">Dell Precision 7670</option>
            <option value="Dell Precision 7680">Dell Precision 7680</option>
            <option value="Dell Precision 7760">Dell Precision 7760</option>
        </select>

        <label for="serial_number">Service Tag.:</label>
        <input type="text" name="serial_number" id="serial_number" required style="text-align:center;">

        <label for="device_processor">Device Processor:</label>
        <input type="text" name="device_processor" id="device_processor" style="text-align:center;">

        <label for="device_storage">Device Storage:</label>
        <select name="device_storage" id="device_storage" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Device Storage --</option>
            <option value="256GB">256GB</option>
            <option value="512GB">512GB</option>
            <option value="1TB">1TB</option>
            <option value="1TB Divided - 512GB">1TB Divided - 512GB</option>
        </select>

        <label for="device_ram">Device RAM:</label>
        <select name="device_ram" id="device_ram" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Device RAM --</option>
            <option value="8GB">8GB</option>
            <option value="16GB">16GB</option>
            <option value="32GB">32GB</option>
            <option value="64GB">64GB</option>
        </select>

        <label for="device_gpu">Device GPU:</label>
        <select name="device_gpu" id="device_gpu" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Device GPU --</option>
            <option value="Intel UHD Graphics">Intel UHD Graphics</option>
            <option value="Intel® Graphics">Intel® Graphics</option>
            <option value="Intel Iris Xe">Intel Iris Xe</option>
            <option value="RTX 1000">RTX 1000</option>
            <option value="RTX 2000">RTX 2000</option>
            <option value="RTX 3000">RTX 3000</option>
            <option value="RTX 3500">RTX 3500</option>
        </select>

        <label for="device_type">Device Type:</label>
        <select name="device_type" id="device_type" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Device Type --</option>
            <option value="All In One PC">All In One PC</option>
            <option value="Standard">Standard</option>
            <option value="Engineering">Engineering</option>
            <option value="SolidWorks">SolidWorks</option>
            <option value="Desktop">Desktop</option>
            <option value="Out of Vertiv Domain">Out of Vertiv Domain</option>
        </select>

        <label for="install_status">Install Status:</label>
        <select name="install_status" id="install_status" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Install Status --</option>
            <option value="In Stock - New">In Stock - New</option>
            <option value="In Stock - Spare">In Stock - Spare</option>
            <option value="Installed">Installed</option>
            <option value="Retired">Retired</option>
        </select>

        <label for="purchased_date">Purchased Date:</label>
        <input type="date" style="text-align: center" name="purchased_date" id="purchased_date" required>

        <label for="device_storage_location">Device Storage Location</label>
        <select name="device_storage_location" id="device_storage_location" required style="text-align:center; text-align-last:center;">
            <option value="" style="text-align:center;">-- Select Location --</option>
            <option value="I.T. Department Main Building">I.T. Department Main Building</option>
            <option value="Stock Room Main Building">Stock Room Main Building</option>
            <option value="Out">Out</option>
        </select>

        <button type="submit" class="btn">Submit</button>
    </form>
</div>

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
