<?php
// edit_toner_drum.php
session_start();
include('db_connect.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch record to edit
    $result = $conn->query("SELECT * FROM toner_drum_inventory WHERE id = $id");
    $row = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $model = $_POST['model'];
    $type = $_POST['type'];
    $printer_name = $_POST['printer_name'];
    $printer_location = $_POST['printer_location'];
    $stock_quantity = $_POST['stock_quantity'];
    $status = $_POST['status'];

    $update = "UPDATE toner_drum_inventory SET model=?, type=?, printer_name=?, printer_location=?, stock_quantity=?, status=? WHERE id=?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssssisi", $model, $type, $printer_name, $printer_location, $stock_quantity, $status, $id);

    if ($stmt->execute()) {
        header("Location: toner_drum_inventory.php");
        exit;
    } else {
        echo "Update failed: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Printer Toner/Drum</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h2>Edit Printer Toner / Drum</h2>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

    <label>Printer Name:</label>
    <input type="text" name="printer_name" value="<?php echo $row['printer_name']; ?>">

    <label>Printer Location:</label>
    <input type="text" name="printer_location" value="<?php echo $row['printer_location']; ?>" required>
    
    <label>Model Name:</label>
    <input type="text" name="model" value="<?php echo $row['model']; ?>" required>

    <label>Type:</label>
    <select name="type" required>
        <option value="Toner" <?php if ($row['type'] == 'Toner') echo 'selected'; ?>>Toner</option>
        <option value="Drum" <?php if ($row['type'] == 'Drum') echo 'selected'; ?>>Drum</option>
        <option value="Cartridge" <?php if ($row['type'] == 'Cartridge') echo 'selected'; ?>>Cartridge</option>
    </select>

    <label>Stock Quantity:</label>
    <input type="number" name="stock_quantity" value="<?php echo $row['stock_quantity']; ?>" required>

    <button type="submit" class="btn">Update</button>
</form>
</body>
</html>
