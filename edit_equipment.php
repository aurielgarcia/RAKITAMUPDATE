<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}
include 'db_connect.php'; // Assumes sqlsrv connection is already set up

// Fetch the equipment to edit
if (isset($_GET['id'])) {
    $equipment_id = $_GET['id'];

    $sql = "SELECT * FROM itequip_inventory.equipment WHERE id = ?";
    $params = array($equipment_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false || !($item = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
        echo "No equipment found!";
        exit();
    }

    // Handle form submission to update equipment
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_equipment'])) {
        $name = $_POST['name'];
        $model = $_POST['model'];
        $storage_location = $_POST['storage_location'];
        $stock_quantity = (int) $_POST['stock_quantity'];
        $remarks = $_POST['remarks'];

        $update_sql = "UPDATE itequip_inventory.equipment 
                       SET name = ?, model = ?, storage_location = ?, stock_quantity = ?, remarks = ? 
                       WHERE id = ?";
        $update_params = array($name, $model, $storage_location, $stock_quantity, $remarks, $equipment_id);
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
            margin-left: 150px;
            transition: background 0.3s ease;
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

        <label for="storage_location">Storage Location</label>
            <select id="storage_location" name="storage_location" required>
            <option value="I.T. Department Main Building" <?php if ($item['storage_location'] == 'I.T. Department Main Building') echo 'selected'; ?>>I.T. Department Main Building</option>
            <option value="Stock Room Main Building" <?php if ($item['storage_location'] == 'Stock Room Main Building') echo 'selected'; ?>>Stock Room Main Building</option>
            <option value="Stock Room IMS Building" <?php if ($item['storage_location'] == 'Stock Room IMS Building') echo 'selected'; ?>>Stock Room IMS Building</option>
            <option value="I.T. Department Main Building & Stock Room Main Building" <?php if ($item['storage_location'] == 'I.T. Department Main Building & Stock Room Main Building') echo 'selected'; ?>>I.T. Department Main Building & Stock Room Main Building</option>
        </select>

        <label for="stock_quantity">Stock Quantity</label>
        <input type="number" id="stock_quantity" name="stock_quantity" value="<?php echo htmlspecialchars($item['stock_quantity']); ?>" required>

        <label for="remarks">Remarks</label>
        <input type="text" id="remarks" name="remarks" value="<?php echo htmlspecialchars($item['remarks']); ?>">

        <button type="submit" name="update_equipment" class="btn">SAVE</button>
    </form>
</div>

</body>
</html>
