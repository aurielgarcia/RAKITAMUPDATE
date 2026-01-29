<?php
session_start();
include('db_connect.php');

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $model = $_POST['model'];
    $color = $_POST['color'];
    $type = $_POST['type'];
    $printer_name = $_POST['printer_name'];
    $printer_location = $_POST['printer_location'];
    $stock_quantity = $_POST['stock_quantity'];

    $update = "UPDATE itequip_inventory.toner_drum_inventory 
               SET model = ?, color = ?, type = ?, printer_name = ?, printer_location = ?, stock_quantity = ?
               WHERE id = ?";
    $params = [$model, $color, $type, $printer_name, $printer_location, $stock_quantity, $id];
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

// Fetch record to edit
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
<html>
<head>
    <title>Edit Printer Toner & Drum</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<style>
    /* Tab header on transparent background */
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
            margin: 20px auto;    /* Reduced margin */
            max-width: 500px;
            width: 100%;
        }

        label {
            font-weight: 600;
            margin-top: 10px;     /* Reduced vertical spacing */
            display: block;
            color: #2c3e50;
            font-size: 13px;      /* Smaller font */
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
            margin-left: 150px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background-color: rgb(4, 115, 15);
        }

</style>
<body>

<div class="sidebar">
  <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
  <hr class="sidebar-divider">
  <a href="rak_toner_drum_inventory.php"><i class="material-icons">arrow_left</i> Printer Toner & Drum Inventory</a>
</div>

<div class="main-content">
<div class="tab-header">Edit Printer Toner & Drum</div>

<form method="POST">
    <input type="hidden" name="id" value="<?= $row['id']; ?>">

    <label>Printer Name:</label>
    <input type="text" name="printer_name" value="<?= htmlspecialchars($row['printer_name']); ?>">

    <label>Printer Location:</label>
    <input type="text" name="printer_location" value="<?= htmlspecialchars($row['printer_location']); ?>" required>

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

</body>
</html>
