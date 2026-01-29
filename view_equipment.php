<?php
session_start();
include 'db_connect.php';

// View Equipment
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['view_equipment'])) {
    $sql = "SELECT e.id, e.name, e.category, e.status, u.username AS added_by, e.date_added FROM equipment e LEFT JOIN users u ON e.added_by = u.id";
    $result = $conn->query($sql);

    $equipment = [];
    while ($row = $result->fetch_assoc()) {
        $equipment[] = $row;
    }
    echo json_encode($equipment);
}
?>