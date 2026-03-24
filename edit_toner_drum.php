<?php
session_start();
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $model = $_POST['model'];
    $color = $_POST['color'];
    $type = $_POST['type'];
    $printer_name = $_POST['printer_name'];
    $site_location = $_POST['site_location'];
    $storage_location = $_POST['storage_location'];
    $stock_quantity = $_POST['stock_quantity'];

    $update = "UPDATE itequip_inventory.toner_drum_inventory 
               SET model = ?, color = ?, type = ?, printer_name = ?, site_location = ?, storage_location = ?, stock_quantity = ?
               WHERE id = ?";
    $params = [$model, $color, $type, $printer_name, $site_location, $storage_location, $stock_quantity, $id];
    $stmt = sqlsrv_prepare($conn, $update, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        header("Location: rak_toner_drum_inventory.php");
        exit;
    } else {
        echo "Update failed.";
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM itequip_inventory.toner_drum_inventory WHERE id = ?";
    $stmt = sqlsrv_prepare($conn, $query, [$id]);
    sqlsrv_execute($stmt);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if (!$row) {
        echo "No record found.";
        exit;
    }
} else {
    echo "No record selected.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Printer Toner & Drum</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
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
            max-width: 350px;
            border-radius: 8px;
        }

        form {
            background: transparent;
            margin: 20px auto;
            max-width: 500px;
            width: 100%;
        }

        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
            color: #2c3e50;
            font-size: 13px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            margin-bottom: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
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
            width: 100%;
            max-width: 150px;
            margin-top: 10px;
            transition: background 0.3s ease;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .btn:hover {
            background-color: rgb(4, 115, 15);
        }
    </style>
</head>
<body>

<div class="sidebar">
    <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
    <hr class="sidebar-divider">
    <a href="rak_toner_drum_inventory.php"><i class="material-icons">arrow_left</i> Back to Inventory</a>
</div>

<div class="main-content">
    <div class="tab-header">Edit Printer Toner & Drum</div>

    <form method="POST">
        <input type="hidden" name="id" value="<?= $row['id']; ?>">

        <label>Printer Name:</label>
        <input type="text" name="printer_name" value="<?= htmlspecialchars($row['printer_name']); ?>">

        <label for="site_location">Site Location:</label>
        <select id="site_location" name="site_location" required onchange="updateStorageOptions()">
            <option value="Al Ghail" <?= ($row['site_location'] == 'Al Ghail') ? 'selected' : ''; ?>>Al Ghail</option>
            <option value="Al Hamra" <?= ($row['site_location'] == 'Al Hamra') ? 'selected' : ''; ?>>Al Hamra</option>
        </select>

        <label for="storage_location">Storage Location:</label>
        <select id="storage_location" name="storage_location" required>
        </select>

        <label>Model Name:</label>
        <input type="text" name="model" value="<?= htmlspecialchars($row['model']); ?>" required>

        <label>Color:</label>
        <select name="color" id="color" required>
            <?php
            $colors = ["Black", "Cyan", "Yellow", "Magenta", "Tri-Color"];
            foreach ($colors as $color) {
                $selected = ($color == $row['color']) ? 'selected' : '';
                echo "<option value=\"$color\" $selected>$color</option>";
            }
            ?>
        </select>

        <label>Type:</label>
        <select name="type" id="type" required>
            <?php
            $types = ["Toner", "Drum", "Cartridge"];
            foreach ($types as $type) {
                $selected = ($type == $row['type']) ? 'selected' : '';
                echo "<option value=\"$type\" $selected>$type</option>";
            }
            ?>
        </select>

        <label>Stock Quantity:</label>
        <input type="number" name="stock_quantity" value="<?= $row['stock_quantity']; ?>" required>

        <button type="submit" class="btn">SAVE</button>
    </form>
</div>

<script>
    const storageOptions = {
        "Al Ghail": [
            "I.T. Department Main Building",
            "Stock Room Main Building",
            "Stock Room IMS Building"
        ],
        "Al Hamra": [
            "I.T. Department Main Building",
            "Storage Room"
        ]
    };

    function updateStorageOptions() {
        const siteSelect = document.getElementById('site_location');
        const storageSelect = document.getElementById('storage_location');
        const selectedSite = siteSelect.value;
        
        storageSelect.innerHTML = '';

        const currentSavedValue = "<?= $row['storage_location']; ?>";

        if (storageOptions[selectedSite]) {
            storageOptions[selectedSite].forEach(opt => {
                const option = document.createElement('option');
                option.value = opt;
                option.textContent = opt;
                if (opt === currentSavedValue) {
                    option.selected = true;
                }
                storageSelect.appendChild(option);
            });
        }
    }

    window.onload = updateStorageOptions;
</script>

</body>
</html>