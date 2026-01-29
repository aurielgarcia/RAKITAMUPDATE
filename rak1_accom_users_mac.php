<?php
include('db_connect.php');

// Function to validate MAC address format
function isValidMac($mac) {
    if ($mac === null) return true; // allow empty (already handled as NULL)
    return (bool)preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = trim($_POST['employee_name']);
    $employee_id = trim($_POST['employee_id']);
    $rak1_accom_room_id = $_POST['rak1_accom_room_id'];
    $mac_address1 = trim($_POST['mac_address1']);
    $mac_address2 = trim($_POST['mac_address2']);
    $created_date = date('Y-m-d H:i:s');

    // Convert empty MAC addresses to NULL
    $mac_address1 = ($mac_address1 === '') ? null : $mac_address1;
    $mac_address2 = ($mac_address2 === '') ? null : $mac_address2;

    // ✅ Validate MAC addresses format
if (!isValidMac($mac_address1) || !isValidMac($mac_address2)) {
    $_SESSION['form_data'] = $_POST; // save all submitted values
    echo "<script>
        alert('Please check the MAC Address. MAC address entered is not valid.');
        window.location.href='rak1_accom_users_mac.php';
    </script>";
    exit;
}

    // Check if mac_address1 or mac_address2 already exists in any room
    $mac_check_sql = "SELECT 1 FROM dbo.rak1_accom_users_mac 
                      WHERE (mac_address1 = ? OR mac_address1 = ? OR mac_address2 = ? OR mac_address2 = ?)
                        AND (mac_address1 IS NOT NULL AND LTRIM(RTRIM(mac_address1)) <> '' AND mac_address1 <> '00:00:00:00:00:00'
                             OR mac_address2 IS NOT NULL AND LTRIM(RTRIM(mac_address2)) <> '' AND mac_address2 <> '00:00:00:00:00:00')";
    $mac_check_stmt = sqlsrv_query($conn, $mac_check_sql, [$mac_address1, $mac_address2, $mac_address1, $mac_address2]);

    if ($mac_check_stmt && sqlsrv_has_rows($mac_check_stmt)) {
        echo "<script>
            alert('One of the MAC addresses is already registered to another room. Cannot assign to this room.');
            window.location.href='rak1_accom_users_mac.php';
        </script>";
        exit;
    }

    // Check current number of valid members in the selected room
    $member_check_sql = "SELECT COUNT(*) AS member_count
                         FROM dbo.rak1_accom_users_mac
                         WHERE rak1_accom_room_id = ?
                           AND ((mac_address1 IS NOT NULL AND LTRIM(RTRIM(mac_address1)) <> '' AND mac_address1 <> '00:00:00:00:00:00')
                                OR (mac_address2 IS NOT NULL AND LTRIM(RTRIM(mac_address2)) <> '' AND mac_address2 <> '00:00:00:00:00:00'))";
    $member_check_stmt = sqlsrv_query($conn, $member_check_sql, [$rak1_accom_room_id]);
    $member_count = 0;

    if ($member_check_stmt !== false && sqlsrv_has_rows($member_check_stmt)) {
        $member_row = sqlsrv_fetch_array($member_check_stmt, SQLSRV_FETCH_ASSOC);
        $member_count = $member_row['member_count'];
    }

    // Get maximum allowed members for this room
    $room_limit_sql = "SELECT number_of_members FROM dbo.rak1_accom_rooms WHERE rak1_accom_room_id = ?";
    $room_limit_stmt = sqlsrv_query($conn, $room_limit_sql, [$rak1_accom_room_id]);
    $max_members = 3; // default fallback
    if ($room_limit_stmt !== false && sqlsrv_has_rows($room_limit_stmt)) {
        $room_row = sqlsrv_fetch_array($room_limit_stmt, SQLSRV_FETCH_ASSOC);
        $max_members = $room_row['number_of_members'];
    }

    // Check if adding this user exceeds maximum members
    if ($member_count >= $max_members) {
        echo "<script>
            alert('Maximum Occupants Reached! Cannot add more users to this room.');
            window.location.href='rak1_accom_users_mac.php';
        </script>";
        exit;
    }

    // If all checks pass, insert new user
    $insert_sql = "INSERT INTO dbo.rak1_accom_users_mac 
                   (employee_name, employee_id, rak1_accom_room_id, mac_address1, mac_address2, created_date)
                   VALUES (?, ?, ?, ?, ?, ?)";
    $insert_result = sqlsrv_query($conn, $insert_sql, [
        $employee_name, $employee_id, $rak1_accom_room_id, $mac_address1, $mac_address2, $created_date
    ]);

    if ($insert_result) {
        echo "<script>
            alert('User added successfully!');
            window.location.href='rak1_accom_users_mac.php';
        </script>";
    } else {
        $errors = sqlsrv_errors();
        echo "<p class='error-message'>Error inserting record: " . $errors[0]['message'] . "</p>";
    }
}

// Fetch floors and rooms
$floors = [];
$rooms_by_floor = [];

$room_query = "SELECT rak1_accom_room_id, floor_number, room_number, wifi_ssid, number_of_members 
               FROM dbo.rak1_accom_rooms
               ORDER BY floor_number, room_number";
$room_result = sqlsrv_query($conn, $room_query);

if ($room_result !== false) {
    while ($row = sqlsrv_fetch_array($room_result, SQLSRV_FETCH_ASSOC)) {
        $floor = $row['floor_number'];
        if (!in_array($floor, $floors)) {
            $floors[] = $floor;
        }
        $rooms_by_floor[$floor][] = $row;
    }
}

// Custom floor order
$floor_order = ['2nd', '1st', 'Ground'];
usort($floors, function($a, $b) use ($floor_order) {
    $posA = array_search($a, $floor_order);
    $posB = array_search($b, $floor_order);
    return $posA - $posB;
});
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User & MAC | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        #particles-js { position: absolute; width: 100%; height: 100%; background-color: #ffffff; z-index: 0; }
        .main-content { margin-left: 200px; padding: 20px; position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; }
        .tab-header { background: rgba(44, 62, 80, 0.6); color: white; padding: 20px 30px; font-size: 18px; font-weight: 600; text-align: center; width: 100%; max-width: 500px; border-radius: 8px; }
        form { background: rgba(152, 199, 246, 0.6); margin: 20px auto; max-width: 500px; width: 100%; padding: 20px; border-radius: 10px; }
        label { font-weight: 600; margin-top: 10px; display: block; color: #2c3e50; font-size: 15px; }
        input, select { width: 100%; padding: 8px; margin-top: 4px; margin-bottom: 12px; border-radius: 8px; font-size: 14px; }
        input:focus, select:focus { border-color: #3498db; outline: none; }
        .btn { background-color: rgb(91, 160, 13); color: white; font-weight: bold; padding: 12px; border: none; border-radius: 10px; cursor: pointer; width: 30%; margin: 20px auto 0; display: block; transition: background 0.3s ease; }
        .btn:hover { background-color: rgb(4, 115, 15); }

        #floor option,
        #rak1_accom_room_id option {
    text-align: left;
        }
    </style>
</head>
<body>
<div id="particles-js"></div>

<div class="sidebar">
    <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="view_accom_user_records.php"><i class="material-icons">arrow_right</i>WIFI Accommodation User Records</a>
    <a href="rak1_accom_rooms.php"><i class="material-icons">arrow_right</i>Add Accommodation Room</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">Add Accommodation User & MAC</div>

    <form action="" method="POST">
        <label for="employee_name">Employee Name:</label>
        <input type="text" name="employee_name" id="employee_name" required style="text-align: center;">

        <label for="employee_id">Employee ID:</label>
        <input type="text" name="employee_id" id="employee_id" required style="text-align: center;">

        <label for="floor">Select Floor:</label>
<select name="floor" id="floor" required style="text-align:center; text-align-last:center;">
    <option value="" style="text-align:center;">-- Select Floor --</option>
    <?php foreach ($floors as $floor): ?>
        <option value="<?= htmlspecialchars($floor); ?>">Floor <?= htmlspecialchars($floor); ?></option>
    <?php endforeach; ?>
</select>

            <label for="rak1_accom_room_id">Select Room (SSID):</label>
            <select name="rak1_accom_room_id" id="rak1_accom_room_id" required style="text-align:center; text-align-last:center;">
                <option value="" style="text-align:center;">-- Select Room --</option>
            </select>

        <label for="mac_address1">MAC Address 1:</label>
        <input type="text" name="mac_address1" id="mac_address1" placeholder="XX:XX:XX:XX:XX:XX" required style="text-align: center;">

        <label for="mac_address2">MAC Address 2:</label>
        <input type="text" name="mac_address2" id="mac_address2" placeholder="XX:XX:XX:XX:XX:XX" style="text-align: center;">

        <button type="submit" class="btn">Submit</button>
    </form>
</div>

<script src="js/particles.min.js"></script>
<script>
particlesJS("particles-js", {
    "particles": { "number": { "value": 80, "density": { "enable": true, "value_area": 800 } }, "color": { "value": "#000000" },
        "shape": { "type": "circle" }, "opacity": { "value": 0.3 }, "size": { "value": 3, "random": true },
        "line_linked": { "enable": true, "distance": 150, "color": "#000000", "opacity": 0.2, "width": 1 }, "move": { "enable": true, "speed": 2 } },
    "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": true, "mode": "grab" } } },
    "retina_detect": true
});
</script>

<script>
    const roomsByFloor = <?= json_encode($rooms_by_floor); ?>;
    const floorSelect = document.getElementById('floor');
    const roomSelect = document.getElementById('rak1_accom_room_id');

    floorSelect.addEventListener('change', function() {
        const selectedFloor = this.value;
        roomSelect.innerHTML = '<option value="">-- Select Room --</option>'; // reset

        if (selectedFloor && roomsByFloor[selectedFloor]) {
            roomsByFloor[selectedFloor].forEach(room => {
                const option = document.createElement('option');
                option.value = room.rak1_accom_room_id;
                option.text = `Room ${room.room_number} (SSID: ${room.wifi_ssid})`;
                roomSelect.appendChild(option);
            });
        }
    });
</script>
</body>
</html>
