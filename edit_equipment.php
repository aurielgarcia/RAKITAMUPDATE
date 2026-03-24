<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}
include 'db_connect.php';

if (isset($_GET['id'])) {
    $equipment_id = $_GET['id'];

    $sql = "SELECT * FROM itequip_inventory.equipment WHERE id = ?";
    $params = array($equipment_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false || !($item = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
        echo "No equipment found!";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_equipment'])) {
        $name = $_POST['name'];
        $model = $_POST['model'];
        $site_location = $_POST['site_location'];
        $storage_location = $_POST['storage_location'];
        $stock_quantity = (int) $_POST['stock_quantity'];
        $remarks = $_POST['remarks'];

        $update_sql = "UPDATE itequip_inventory.equipment 
                       SET name = ?, model = ?, site_location = ?, storage_location = ?, stock_quantity = ?, remarks = ? 
                       WHERE id = ?";
        $update_params = array($name, $model, $site_location, $storage_location, $stock_quantity, $remarks, $equipment_id);
        $update_stmt = sqlsrv_query($conn, $update_sql, $update_params);

        if ($update_stmt) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error updating equipment: ";
            print_r(sqlsrv_errors());
        }
    }
} else {
    echo "No ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Equipment</title>
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
    <a href="dashboard.php"><i class="material-icons">arrow_left</i> IT Equipment Inventory</a>
</div>

<div class="main-content">
    <div class="tab-header">Edit IT Equipment</div>

    <form action="edit_equipment.php?id=<?php echo htmlspecialchars($item['id']); ?>" method="POST">
        <label for="name">Item Description</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>

        <label for="model">Model</label>
        <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($item['model']); ?>" required>

        <label for="site_location">Site Location</label>
        <select id="site_location" name="site_location" required onchange="updateStorageOptions()">
            <option value="Al Ghail" <?php if ($item['site_location'] == 'Al Ghail') echo 'selected'; ?>>Al Ghail</option>
            <option value="Al Hamra" <?php if ($item['site_location'] == 'Al Hamra') echo 'selected'; ?>>Al Hamra</option>
        </select>

        <label for="storage_location">Storage Location</label>
        <select id="storage_location" name="storage_location" required>
        </select>

        <label for="stock_quantity">Stock Quantity</label>
        <input type="number" id="stock_quantity" name="stock_quantity" value="<?php echo htmlspecialchars($item['stock_quantity']); ?>" required>

        <label for="remarks">Remarks</label>
        <input type="text" id="remarks" name="remarks" value="<?php echo htmlspecialchars($item['remarks']); ?>">

        <button type="submit" name="update_equipment" class="btn">SAVE</button>
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

        const currentSavedValue = "<?php echo $item['storage_location']; ?>";

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