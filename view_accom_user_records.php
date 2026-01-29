<?php
include('db_connect.php');

// Fetch all rooms, grouped by floor
$rooms = [];
$room_query = "SELECT rak1_accom_room_id, floor_number, room_number, wifi_ssid, number_of_members 
               FROM dbo.rak1_accom_rooms 
               ORDER BY floor_number, room_number";
$room_result = sqlsrv_query($conn, $room_query);
if ($room_result !== false) {
    while ($row = sqlsrv_fetch_array($room_result, SQLSRV_FETCH_ASSOC)) {
        $rooms[$row['floor_number']][] = $row; // Group by floor_number
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Accommodation Users Records | VERTIV GULF LLC ITMS</title>
<link rel="stylesheet" href="styles.css">
<link rel="icon" type="image/png" href="favicon.jpg">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>

  body {
  background-color:rgb(206, 215, 230); /* Change to your desired color */
}

table {
    width: 100%;           /* full width of room box */
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 15px;       /* slightly bigger text */
}

.main-content { 
    margin-left: 200px; 
    padding: 20px; position: 
    relative; z-index: 1; 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
}

.tab-header {
    background: black;
    color: white;
    padding: 20px 30px;
    font-size: 25px;
    font-weight: 600;
    text-align: center;
    width: 100%;
    max-width: 700px;
    border-radius: 8px;
    margin-bottom: 20px;
    cursor: default;

    display: flex;
    flex-direction: column;   /* stack title and search bar */
    align-items: center;      /* center horizontally */
    gap: 10px;                /* spacing between title and search */
}

.floor-header { 
    background: rgba(91, 160, 13, 0.8); 
    color: white; 
    padding: 10px 15px; 
    font-weight: 700; 
    margin-top: 10px;
    margin-bottom: 10px; 
    width: 100%; 
    max-width: 300px; 
    border-radius: 8px; 
    cursor: pointer; 
    text-align: center;
}

.room-box {
    width: 100%;           /* allow table to occupy full width */
    max-width: 900px;      /* increase max width for bigger table */
    padding: 13px;
    border-radius: 10px;
    margin-bottom: 20px;
    background: white;
}

.room-title { 
    font-weight: 700; 
    font-size: 20px; 
    margin-bottom: 5px; 
    color: #2c3e50; 
    cursor: pointer; 
}

td:first-child {
    text-align: left;   /* first column */
}

td:not(:first-child) {
    text-align: center; /* all other columns */
}

th, td {
    border: 1px solid #2c3e50;
    padding: 10px 12px;    /* larger padding for readability */
}

th {
    background-color: rgba(44, 62, 80, 0.8); /* slightly darker for contrast */
    color: #fff;
    font-size: 16px;       /* bigger header text */
}

td {
    font-size: 15px;       /* bigger content text */
    background: #fff;
}

.hidden { display: none; }

.action-icons a {
    text-decoration: none; /* removes underline */
}

.action-icons i {
    color: black;
    cursor: pointer;
    transition: color 0.2s ease-in-out;
    margin-right: 6px;
}

.action-icons i.edit-icon:hover {
    color: blue;
}

.action-icons i.delete-icon:hover {
    color: red;
}

.modal {
    position: fixed;
    top: 0; 
    left: 0;
    width: 100%; 
    height: 100%;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal.hidden {
    display: none;
}

.modal-content {
    background: #fff; /* entire modal is one box */
    padding: 25px 30px; /* padding applies to everything, including title & close btn */
    border-radius: 10px;
    width: 400px;
    max-width: 90%;
    position: relative;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 22px;
    cursor: pointer;
    color: #333;
}

.modal-content h3 {
    margin-top: 0;
    text-align: center;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px; /* spacing between title and inputs */
    background: transparent; /* ensure no separate background */
}

.modal-content input {
    width: 100%; 
    padding: 10px 12px; 
    margin-bottom: 15px;
    border: 1px solid #ccc; 
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.modal-content input:focus {
    border-color: #3498db;
    outline: none;
}

.modal-content button {
    padding: 10px 18px; 
    background: #2c3e50; 
    color: white; 
    border: none; 
    border-radius: 6px; 
    cursor: pointer;
    font-weight: 500;
    transition: background 0.2s;
}

.modal-content button:hover {
    background: #34495e;
}

.modal-actions {
    display: flex;
    justify-content: center; /* Center the buttons */
    gap: 10px; /* Space between buttons if you have multiple */
    margin-top: 20px;
}

.btn-cancel {
    background: #e74c3c;
}

.btn-cancel:hover {
    background: #c0392b;
}

#searchInput {
    float: none;              /* remove float */
    width: 200px;             /* adjust as needed */
    padding: 6px 10px;
    font-size: 14px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

</style>
</head>
<body>


<div class="sidebar">
    <img src="images/vertiv-logo1.png" class="logo" alt="Vertiv Logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="rak1_accom_users_mac.php"><i class="material-icons">arrow_right</i>Add User & MAC</a>
    <a href="rak1_accom_rooms.php"><i class="material-icons">arrow_right</i>Add Accommodation Room</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="tab-header">
    WIFI Accommodation User Records
    <input type="text" id="searchInput" placeholder="Search...">
</div>

    <?php foreach ($rooms as $floor => $floor_rooms): ?>
        <div class="floor-header" onclick="toggleFloor('floor<?= $floor ?>')">
            Floor <?= htmlspecialchars($floor) ?>
        </div>
        <div id="floor<?= $floor ?>" class="floor-rooms hidden">
            <?php foreach ($floor_rooms as $room): ?>
                <?php $room_id = 'room_'.$room['rak1_accom_room_id']; ?>
                <div class="room-box">
                   <div class="room-title" onclick="toggleRoom('<?= $room_id ?>', 'floor<?= $floor ?>')">
    Room <?= htmlspecialchars($room['room_number']); ?> •
    <span style="color: #686cedff; font-weight: medium;">
        SSID: <?= htmlspecialchars($room['wifi_ssid']); ?> •
        <span style="color: #555; font-weight: normal;">
       Max Occupants: <?= htmlspecialchars($room['number_of_members']); ?>
    </span>
</div>
                    <?php
                        $user_query = "SELECT employee_name, employee_id, mac_address1, mac_address2 
                                       FROM dbo.rak1_accom_users_mac 
                                       WHERE rak1_accom_room_id = ? 
                                       ORDER BY employee_name";
                        $user_stmt = sqlsrv_query($conn, $user_query, [$room['rak1_accom_room_id']]);
                    ?>
                    <div id="<?= $room_id ?>" class="hidden room-content">
                        <?php if ($user_stmt && sqlsrv_has_rows($user_stmt)): ?>
                            <table>
    <thead>
        <tr>
            <th>Employee Name</th>
            <th>Employee ID</th>
            <th>MAC Address 1</th>
            <th>MAC Address 2</th>
            <th>Action</th> <!-- New column -->
        </tr>
    </thead>
    <tbody>
        <?php while ($user = sqlsrv_fetch_array($user_stmt, SQLSRV_FETCH_ASSOC)): ?>
            <tr>
                <td><?= htmlspecialchars($user['employee_name']); ?></td>
                <td><?= htmlspecialchars($user['employee_id']); ?></td>
                <td><?= htmlspecialchars($user['mac_address1']); ?></td>
                <td><?= htmlspecialchars($user['mac_address2']); ?></td>
                <td class="action-icons">
    <i class="material-icons edit-icon" title="Edit"
       onclick="openEditModal(
           '<?= htmlspecialchars($user['employee_id']); ?>',
           '<?= htmlspecialchars($user['employee_name']); ?>',
           '<?= htmlspecialchars($user['employee_id']); ?>',
           '<?= htmlspecialchars($user['mac_address1']); ?>',
           '<?= htmlspecialchars($user['mac_address2']); ?>'
       )">edit</i>
    <a href="delete_rak1_accom_user.php?employee_id=<?= urlencode($user['employee_id']); ?>"
       onclick="return confirm('Are you sure you want to delete this user?');" 
       title="Delete">
        <i class="material-icons delete-icon">delete</i>
    </a>
</td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
                        <?php else: ?>
                            <p>No users assigned to this room.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>


<!-- Edit User Modal -->
<div id="editModal" class="modal hidden">
    <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h3>Edit User</h3>
    <form id="editForm" method="POST" action="edit_rak1_accom_user.php">
            <input type="hidden" name="employee_id" id="edit_employee_id">
            <label for="employee_name">Employee Name</label>
            <input type="text" name="employee_name" id="edit_employee_name" required>

            <label for="employee_id_input">Employee ID</label>
            <input type="text" name="employee_id_input" id="edit_employee_id_input" required>

            <label for="mac_address1">MAC Address 1</label>
            <input type="text" name="mac_address1" id="edit_mac_address1">

            <label for="mac_address2">MAC Address 2</label>
            <input type="text" name="mac_address2" id="edit_mac_address2">

            <div class="modal-actions">
            <button type="submit" class="btn-save">Save Changes</button>
        </div>
    </form>
</div>



<script>
// Toggle floor
function toggleFloor(floorId) {
    var element = document.getElementById(floorId);
    element.classList.toggle("hidden");
}

// Toggle room (only one open per floor)
function toggleRoom(roomId, floorId) {
    var floorElement = document.getElementById(floorId);
    var allRooms = floorElement.querySelectorAll(".room-content");

    allRooms.forEach(function(room) {
        if (room.id !== roomId) {
            room.classList.add("hidden"); // hide all other rooms
        }
    });

    var targetRoom = document.getElementById(roomId);
    targetRoom.classList.toggle("hidden"); // toggle clicked one
}

// Search filter
document.getElementById("searchInput").addEventListener("keyup", function() {
    var filter = this.value.toLowerCase();
    var floors = document.querySelectorAll(".floor-rooms");

    floors.forEach(function(floor) {
        var rooms = floor.querySelectorAll(".room-box");
        var floorHasMatch = false;

        rooms.forEach(function(room) {
            var text = room.innerText.toLowerCase();
            var roomContent = room.querySelector(".room-content");

            if (filter && text.indexOf(filter) > -1) {
                room.style.display = ""; // show matching room
                floorHasMatch = true;

                // Expand room content automatically if filtered
                if (roomContent && roomContent.classList.contains("hidden")) {
                    roomContent.classList.remove("hidden");
                }
            } else {
                room.style.display = filter ? "none" : ""; // hide only during filtering
                // Collapse room content when filtering or empty search
                if (roomContent && !roomContent.classList.contains("hidden")) {
                    roomContent.classList.add("hidden");
                }
            }
        });

        // Show or hide floor header based on matches
        var floorHeader = floor.previousElementSibling;
        if (filter) {
            floorHeader.style.display = floorHasMatch ? "" : "none";
            floor.style.display = floorHasMatch ? "" : "none";
        } else {
            floorHeader.style.display = ""; // reset floor header visibility
            floor.style.display = "hidden"; // keep collapsed by default
        }
    });
});

</script>


<script>
function openEditModal(employee_id, employee_name, emp_id_input, mac1, mac2) {
    document.getElementById('edit_employee_id').value = employee_id;
    document.getElementById('edit_employee_name').value = employee_name;
    document.getElementById('edit_employee_id_input').value = emp_id_input;
    document.getElementById('edit_mac_address1').value = mac1;
    document.getElementById('edit_mac_address2').value = mac2;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>


</body>
</html>
