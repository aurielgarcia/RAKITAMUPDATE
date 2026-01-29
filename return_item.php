<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate required POST fields
    $checkout_id = isset($_POST['checkout_id']) ? intval($_POST['checkout_id']) : 0;
    $action_type = isset($_POST['action_type']) ? $_POST['action_type'] : '';

    if ($checkout_id <= 0 || !in_array($action_type, ['return', 'faulty'])) {
        die("Invalid input.");
    }

    // Initialize SQL and parameters
    $update_status_sql = '';
    $status_params = [];

    if ($action_type === 'return') {
        $equipment_id = isset($_POST['equipment_id']) ? intval($_POST['equipment_id']) : 0;
        $quantity_checked_out = isset($_POST['quantity_checked_out']) ? intval($_POST['quantity_checked_out']) : 0;

        if ($equipment_id <= 0 || $quantity_checked_out <= 0) {
            die("Invalid equipment data.");
        }

        // Update stock quantity
        $update_stock_sql = "UPDATE itequip_inventory.equipment SET stock_quantity = stock_quantity + ? WHERE id = ?";
        $stock_params = [$quantity_checked_out, $equipment_id];
        $stmt_stock = sqlsrv_prepare($conn, $update_stock_sql, $stock_params);

        if (!$stmt_stock || !sqlsrv_execute($stmt_stock)) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Mark checkout as returned
        $update_status_sql = "UPDATE itequip_inventory.checkouts SET return_status = 'returne', return_date = GETDATE() WHERE id = ?";
        $status_params = [$checkout_id];
    } elseif ($action_type === 'faulty') {
        // Mark checkout as returned faulty
        $update_status_sql = "UPDATE itequip_inventory.checkouts SET return_status = 'returned_faulty' WHERE id = ?";
        $status_params = [$checkout_id];
    }

    // Execute return status update
    $stmt_status = sqlsrv_prepare($conn, $update_status_sql, $status_params);
    if (!$stmt_status || !sqlsrv_execute($stmt_status)) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Redirect after processing
    header("Location: view_checkouts.php");
    exit();
}
?>
