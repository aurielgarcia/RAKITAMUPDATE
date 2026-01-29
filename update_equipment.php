<?php
session_start();
include 'db_connect.php';

// Update Equipment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_equipment'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $status = $_POST['status'];

    $sql = "UPDATE equipment SET name=?, category=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $category, $status, $id);
    if ($stmt->execute()) {
        echo "Equipment updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>