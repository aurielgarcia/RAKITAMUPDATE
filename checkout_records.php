<?php
include 'db_connect.php';

$sql = "SELECT checkouts.id, equipment.name AS equipment_name, checkouts.checkout_date, 
               checkouts.quantity_checked_out, checkouts.requested_by 
        FROM checkouts
        JOIN equipment ON checkouts.equipment_id = equipment.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Records</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Checkout Records</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Checkout Date</th>
                <th>Quantity Checked Out</th>
                <th>Requested By</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['equipment_name']; ?></td>
                <td><?php echo $row['checkout_date']; ?></td>
                <td><?php echo $row['quantity_checked_out']; ?></td>
                <td><?php echo $row['requested_by']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
