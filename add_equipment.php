<?php
// Include database connection
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $order_date = $_POST['order_date'];
    $quantity_ordered = $_POST['quantity_ordered'];
    $delivery_date = $_POST['delivery_date'];
    $quantity_delivered = $_POST['quantity_delivered'];
    $status = $_POST['status'];
    $supplier = $_POST['supplier'];

    // Check if the equipment already exists
    $sql_check = "SELECT stock_quantity FROM equipment WHERE name = '$name'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // If equipment exists, update the stock quantity
        $row = $result_check->fetch_assoc();
        $current_stock = $row['stock_quantity'];

        // Calculate the new stock quantity
        $new_stock_quantity = $current_stock + $quantity_delivered;

        // Update stock quantity for the existing equipment
        $sql_update = "UPDATE equipment SET stock_quantity = '$new_stock_quantity' WHERE name = '$name'";
        
        if ($conn->query($sql_update) === TRUE) {
            echo "Stock quantity updated successfully!";
        } else {
            echo "Error updating stock quantity: " . $conn->error;
        }
    } else {
        // If equipment doesn't exist, insert new equipment
        $sql_insert = "INSERT INTO equipment (name, order_date, quantity_ordered, delivery_date, quantity_delivered, status, supplier, stock_quantity)
                       VALUES ('$name', '$order_date', '$quantity_ordered', '$delivery_date', '$quantity_delivered', '$status', '$supplier', '$quantity_delivered')";
        
        if ($conn->query($sql_insert) === TRUE) {
            echo "New equipment added successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Equipment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Add New Equipment</h2>
    <form method="POST" action="add_equipment.php">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="order_date">Order Date:</label><br>
        <input type="date" id="order_date" name="order_date" required><br><br>

        <label for="quantity_ordered">Quantity Ordered:</label><br>
        <input type="number" id="quantity_ordered" name="quantity_ordered" required><br><br>

        <label for="delivery_date">Delivery Date:</label><br>
        <input type="date" id="delivery_date" name="delivery_date" required><br><br>

        <label for="quantity_delivered">Quantity Delivered:</label><br>
        <input type="number" id="quantity_delivered" name="quantity_delivered" required><br><br>

        <label for="status">Status:</label><br>
        <select id="status" name="status" required>
            <option value="Available">Available</option>
            <option value="Need To Order">Need To Order</option>
            <option value="Out of Stock">Out of Stock</option>
        </select><br><br>

        <label for="supplier">Supplier:</label><br>
        <input type="text" id="supplier" name="supplier" required><br><br>

        <input type="submit" value="Add Equipment">
    </form>

    <br>
    <a href="dashboard.php">Back to Dashboard</a> <!-- Link to navigate back to the Dashboard -->
</body>
</html>
