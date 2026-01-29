<?php
include('db_connect.php');

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("No printer selected.");
}

$id = $_GET['id'];

// Fetch the printer details
$sql = "SELECT printer_model, printer_serialnumber, printer_ip_address, printer_subnet_mask, printer_gateway, printer_dns1, printer_dns2,
               printer_switch, printer_port, printer_web_portal_admin_credentials
        FROM dbo.printer_inventory
        WHERE id = ?";
$params = [$id];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$printer = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$printer) {
    die("Printer not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Printer Specifications | RAK ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            background-size: cover;
            background-position: center;
            z-index: 0;
        }
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
            font-weight: 700;
            text-align: center;
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
        }
        .details-box {
            background: rgba(126, 171, 242, 0.6);
            margin: 20px auto;
            max-width: 500px;
            width: 80%;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 700;
        }
        label {
            text-align: left;
            font-weight: 600;
            display: block;
            color: #2c3e50;
            font-size: 18px;
            margin-top: 2px;
            margin-bottom: 5px;
        }
        p {
            margin-bottom: 12px;
            padding: 8px;
            background: white;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div id="particles-js"></div>

<div class="sidebar">
    <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="printer_inventory.php"><i class="material-icons">arrow_left</i>Back to Printer Inventory</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">Printer Specifications</div>

    <div class="details-box">
        
        <label>Printer Model:</label>
        <p><?php echo htmlspecialchars($printer['printer_model']); ?></p>

        <label>Serial Number:</label>
        <p><?php echo htmlspecialchars($printer['printer_serialnumber']); ?></p>

        <label>IP Address:</label>
        <p>
            <?php if (!empty($printer['printer_ip_address']) && $printer['printer_ip_address'] !== 'N/A'): ?>
                <a href="http://<?php echo htmlspecialchars($printer['printer_ip_address']); ?>" target="_blank">
                    <?php echo htmlspecialchars($printer['printer_ip_address']); ?>
                </a>
            <?php else: ?>
                <?php echo htmlspecialchars($printer['printer_ip_address']); ?>
            <?php endif; ?>
        </p>
        <label>Subnet Mask:</label>
        <p><?php echo htmlspecialchars($printer['printer_subnet_mask']); ?></p>

        <label>Gateway:</label>
        <p><?php echo htmlspecialchars($printer['printer_gateway']); ?></p>

        <label>DNS 1:</label>
        <p><?php echo htmlspecialchars($printer['printer_dns1']); ?></p>

        <label>DNS 2:</label>
        <p><?php echo htmlspecialchars($printer['printer_dns2']); ?></p>

        <label>Switch:</label>
        <p><?php echo htmlspecialchars($printer['printer_switch']); ?></p>

        <label>Port:</label>
        <p><?php echo htmlspecialchars($printer['printer_port']); ?></p>

        <label>Web Portal Admin Credentials:</label>
        <p><?php echo htmlspecialchars($printer['printer_web_portal_admin_credentials']); ?></p>
    </div>
</div>

<script src="js/particles.min.js"></script>
<script>
  particlesJS("particles-js", {
    "particles": {
      "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
      "color": { "value": "#000000" },
      "shape": { "type": "circle", "stroke": { "width": 0 }, "polygon": { "nb_sides": 5 } },
      "opacity": { "value": 0.3 },
      "size": { "value": 3, "random": true },
      "line_linked": { "enable": true, "distance": 150, "color": "#000000", "opacity": 0.2, "width": 1 },
      "move": { "enable": true, "speed": 2, "out_mode": "out" }
    },
    "interactivity": {
      "events": { "onhover": { "enable": true, "mode": "grab" }, "resize": true },
      "modes": { "grab": { "distance": 140, "line_linked": { "opacity": 0.4 } } }
    },
    "retina_detect": true
  });
</script>

</body>
</html>
