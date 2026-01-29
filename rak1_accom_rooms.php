<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $floor_number = $_POST['floor_number'];
    $room_number = $_POST['room_number'];
    $number_of_members = (int)$_POST['number_of_members'];
    $wifi_ssid = $_POST['wifi_ssid'];

    // Insert new room
    $insert_sql = "INSERT INTO dbo.rak1_accom_rooms (floor_number, room_number, number_of_members, wifi_ssid) 
                   VALUES (?, ?, ?, ?)";
    $params = [$floor_number, $room_number, $number_of_members, $wifi_ssid];
    $insert_result = sqlsrv_query($conn, $insert_sql, $params);

    if ($insert_result) {
        echo "<script>
            alert('Room added successfully!');
            window.location.href = 'rak1_accom_rooms.php';
        </script>";
    } else {
        $errors = sqlsrv_errors();
        echo "<p class='error-message'>Error inserting record: " . $errors[0]['message'] . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Accommodation Room | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
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
            font-weight: 600;
            text-align: center;
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
        }

        form {
            background: rgba(152, 199, 246, 0.6);
            margin: 20px auto;
            max-width: 500px;
            width: 100%;
            padding: 20px;
            border-radius: 10px;
        }

        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
            color: #2c3e50;
            font-size: 15px;
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
            margin: 20px auto 0;
            display: block;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background-color: rgb(4, 115, 15);
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
    <a href="rak1_accom_users_mac.php"><i class="material-icons">arrow_right</i>Add User & MAC</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">Add Accommodation Room</div>

    <form action="rak1_accom_rooms.php" method="POST">
        <label for="floor_number">Floor Number:</label>
        <select name="floor_number" id="floor_number" required style="text-align: center;">
            <option value="">-- Select Floor --</option>
            <option value="Ground">Ground</option>
            <option value="1st">1st</option>
            <option value="2nd">2nd</option>
        </select>

        <label for="room_number">Room Number:</label>
        <input type="text" id="room_number" name="room_number" required style="text-align: center;">

        <label for="number_of_members">Number of Occupants:</label>
        <input type="number" id="number_of_members" name="number_of_members" min="1" required style="text-align: center;">

        <label for="wifi_ssid">WiFi SSID:</label>
        <input type="text" id="wifi_ssid" name="wifi_ssid" required style="text-align: center;">

        <button type="submit" class="btn">Submit</button>
    </form>
</div>

<script src="js/particles.min.js"></script>
<script>
particlesJS("particles-js", {
    "particles": {
        "number": { "value": 80, "density": { "enable": true, "value_area": 800 }},
        "color": { "value": "#000000" },
        "shape": { "type": "circle" },
        "opacity": { "value": 0.3 },
        "size": { "value": 3, "random": true },
        "line_linked": { "enable": true, "distance": 150, "color": "#000000", "opacity": 0.2, "width": 1 },
        "move": { "enable": true, "speed": 2 }
    },
    "interactivity": {
        "detect_on": "canvas",
        "events": { "onhover": { "enable": true, "mode": "grab" } }
    },
    "retina_detect": true
});
</script>

</body>
</html>
