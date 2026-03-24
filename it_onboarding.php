<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Onboarding | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow-y: auto; /* allow scroll only if needed */
        }

        .table-wrapper {
            height: calc(100.9vh - 200px); /* use height instead of max-height */
            overflow-y: auto; /* only this scrolls */
            overflow-x: auto;
            border: 2px solid #ddd;
            box-sizing: border-box;
            width: 100%;
        }

    .main-content {
        margin-left: 220px;
        padding: 20px;
    }

    .button-wrapper {
        text-align: center;
        margin-bottom: 20px;
    }

    .button-wrapper2 {
        text-align: right;
        margin-bottom: 5px;
    }

    table {
            width: 100%;        /* Expand with screen */
            min-width: 1400px;  /* Prevent shrinking too small */
            border-collapse: collapse;
            font-size: 13px;
        }

    th, td {
        border: 1px solid #ccc;
        padding: 15px;
        text-align: left;
        white-space: nowrap; 
    }

    th {
        background: #343a40;
        color: white;
        position: sticky;
        top: 0;
        font-size: 15px;
    }

    td {
        font-size: 13px;
    }

    tr:hover {
        background: #f0f8ff;
    }

    .modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0,0,0,0.5); 
}

    .modal-content {
    background-color: #fff;
    margin: 10% auto; 
    padding: 20px;
    border: 1px solid #888;
    width: 30%;
    max-width: 700px;
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0,0,0,0.3);
    position: relative;
}

    .modal .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 28px;
        cursor: pointer;
    }

    form {
    background: none !important;
    box-shadow: none !important;
    margin: 0;
    padding: 0;
    border: none;
}

    .modal input,
    .modal select {
        width: 100%;
        padding: 15px;
        margin: 5px 0 12px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .modal button {
        background: #2980b9; 
        color: white; 
        padding: 15px 30px;
        border: none; 
        border-radius: 5px; 
        cursor: pointer;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .action-btn {
        padding: 4px 8px;
        margin-right: 5px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        color: white;
        font-size: 12px;
    }

    .onboarded-btn {
    background: green;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: transform 0.3s ease, background 0.3s ease;
}

/* Hover effect */
.onboarded-btn:hover {
    transform: scale(1.1);  /* slightly enlarges the button */
    background: #28a745;    /* slightly brighter green */
}

    .cancelled-btn {
    background: red;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: transform 0.3s ease, background 0.3s ease;
}

.cancelled-btn:hover {
    transform: scale(1.1);
    background: #e63946; /* brighter red */
}

/* Edit button */
.edit-btn {
    background: orange;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: transform 0.3s ease, background 0.3s ease;
}

.edit-btn:hover {
    transform: scale(1.1);
    background: #ffa500; /* brighter orange */
}

        table#stagingTable th:nth-child(1),
        table#stagingTable th:nth-child(2),
        table#stagingTable th:nth-child(3),
        table#stagingTable th:nth-child(4),
        table#stagingTable th:nth-child(5),
        table#stagingTable th:nth-child(6),
        table#stagingTable th:nth-child(7),
        table#stagingTable th:nth-child(8),
        table#stagingTable th:nth-child(9),
        table#stagingTable th:nth-child(10) {
            text-align: center;
        }

        table#stagingTable td:nth-child(1) {
            text-align: left;
        }

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="it_asset_count.php"><i class="material-icons">arrow_right</i>IT Asset Count</a>
    <a href="it_onboarding_lists.php"><i class="material-icons">arrow_right</i>Onboarded List</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i>Logout</a>
</div>

<div class="main-content">
    <div class="button-wrapper2">
        <button id="addJoinerBtn"><i class="fas fa-plus"></i> Add New Joiner</button>
    </div>

    <div class="tab-header">RAK - IT Onboarding Device Requirements</div>

    <div class="table-wrapper">
        <table id="stagingTable">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Employee ID</th>
                    <th>Designation</th>
                    <th>Department</th>
                    <th>Manager's Name</th>
                    <th>Joining Date</th>
                    <th>SNOW Ticket</th>
                    <th>PC Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- New joiners will appear here -->
            </tbody>
        </table>
    </div>
</div>

<!-- ADD JOINER MODAL -->
<div id="addJoinerModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('addJoinerModal')">&times;</span>
    <h2>Add New Joiner</h2>
    <form id="addJoinerForm">
      <label>Full Name:</label>
      <input type="text" name="name" required style="text-align:center;">

      <label>Employee ID:</label>
      <input type="text" name="employee_id" required style="text-align:center;">

      <label>Role / Designation:</label>
      <input type="text" name="role" required style="text-align:center;"> 

      <label>Department:</label>
      <input type="text" name="department" required style="text-align:center;">

      <label>Manager’s Name:</label>
      <input type="text" name="manager_name" required style="text-align:center;">

      <label>Joining Date (YYYY-MM-DD or TBC):</label>
      <input type="text" name="joining_date" placeholder="YYYY-MM-DD or TBC" style="text-align:center;">

      <label>SNOW Ticket:</label>
      <input type="text" name="snow_ticket" placeholder="Optional" style="text-align:center;">

      <label>PC Type:</label>
      <select name="pc_type" required style="text-align:center;">
        <option value="">-- Select PC Type --</option>
        <option value="All In One PC">All In One PC</option>
        <option value="Engineering">Engineering</option>
        <option value="SolidWorks">SolidWorks</option>
        <option value="Standard">Standard</option>
      </select>

      <!-- FIXED: name="onboarding_status" -->
      <label>Status:</label>
      <select name="onboarding_status" required style="text-align:center;">
        <option value="">-- Select Status --</option>
        <option value="New Joiner">New Joiner</option>
        <option value="Existing - Approved">Existing - Approved</option>
        <option value="Now Onboarding">Now Onboarding</option>
      </select>

      <div class="button-wrapper">
        <button type="submit">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT JOINER MODAL -->
<div id="editJoinerModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('editJoinerModal')">&times;</span>
    <h2>Edit Joiner</h2>
    <form id="editJoinerForm">
      <input type="hidden" name="employee_id">

      <label>Full Name:</label>
      <input type="text" name="name" required style="text-align:center;">

      <label>Role / Designation:</label>
      <input type="text" name="role" required style="text-align:center;">

      <label>Department:</label>
      <input type="text" name="department" required style="text-align:center;">

      <label>Manager’s Name:</label>
      <input type="text" name="manager_name" required style="text-align:center;">

      <label>Joining Date (YYYY-MM-DD or TBC):</label>
      <input type="text" name="joining_date" placeholder="YYYY-MM-DD or TBC" style="text-align:center;">

      <label>SNOW Ticket:</label>
      <input type="text" name="snow_ticket" placeholder="Optional" style="text-align:center;">

      <label>PC Type:</label>
      <select name="pc_type" required style="text-align:center;">
        <option value="">-- Select PC Type --</option>
        <option value="All In One PC">All In One PC</option>
        <option value="Engineering">Engineering</option>
        <option value="SolidWorks">SolidWorks</option>
        <option value="Standard">Standard</option>
      </select>

      <!-- FIXED: name="onboarding_status" -->
      <label>Status:</label>
      <select name="onboarding_status" required style="text-align:center;">
        <option value="">-- Select Status --</option>
        <option value="New Joiner">New Joiner</option>
        <option value="Existing - Approved">Existing - Approved</option>
        <option value="Now Onboarding">Now Onboarding</option>
      </select>

      <div class="button-wrapper">
        <button type="submit">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
const tbody = document.querySelector('#stagingTable tbody');

// ----- OPEN/CLOSE MODALS -----
function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

document.getElementById('addJoinerBtn').addEventListener('click', () => {
  document.getElementById('addJoinerForm').reset();
  document.getElementById('addJoinerModal').style.display = 'block';
});

window.onclick = e => {
  if (e.target.classList.contains('modal')) e.target.style.display = 'none';
};

// ----- CREATE TABLE ROW -----
function createTableRow(data) {
  const row = document.createElement('tr');
  row.innerHTML = `
    <td>${data.name}</td>
    <td>${data.employee_id}</td>
    <td>${data.role}</td>
    <td>${data.department}</td>
    <td>${data.manager_name}</td>
    <td>${data.joining_date}</td>
    <td>${data.snow_ticket}</td>
    <td>${data.pc_type}</td>
    <td>${data.onboarding_status}</td>
    <td>
      <button class="action-btn onboarded-btn">Onboard</button>
      <button class="action-btn cancelled-btn">Cancel</button>
      <button class="action-btn edit-btn">Edit</button>
    </td>
  `;
  attachRowButtons(row);
  return row;
}

// ----- LOAD JOINERS -----
function loadJoiners() {
  fetch('get_it_onboarding.php')
    .then(res => res.json())
    .then(data => {
      tbody.innerHTML = '';
      data.forEach(joiner => {
        if (['New Joiner', 'Existing - Approved', 'Now Onboarding'].includes(joiner.onboarding_status)) {
          tbody.appendChild(createTableRow(joiner));
        }
      });
    })
    .catch(err => console.error(err));
}

// ----- ADD NEW JOINER -----
document.getElementById('addJoinerForm').addEventListener('submit', e => {
  e.preventDefault();
  const form = e.target;
  const data = Object.fromEntries(new FormData(form));

  fetch('add_it_onboarding.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ joiners: [data] })
  })
  .then(res => res.json())
  .then(result => {
    if (result.success) {
      alert('Joiner added successfully.');
      if (['New Joiner','Existing - Approved','Now Onboarding'].includes(data.onboarding_status)) {
        tbody.appendChild(createTableRow(data));
      }
      form.reset();
      closeModal('addJoinerModal');
    } else alert('Error: ' + result.message);
  })
  .catch(err => {
    console.error('Add error', err);
    alert('Unexpected JS error. Check console for details.');
  });
});

// ----- EDIT EXISTING JOINER -----
document.getElementById('editJoinerForm').addEventListener('submit', e => {
  e.preventDefault();
  const form = e.target;
  const joinerData = Object.fromEntries(new FormData(form));

  fetch('edit_it_onboarding.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ joiner: joinerData })
  })
  .then(res => res.json())
  .then(result => {
    if (result.success) {
      alert('Joiner updated successfully.');
      loadJoiners();
      form.reset();
      closeModal('editJoinerModal');
    } else alert('Error: ' + result.message);
  })
  .catch(err => {
    console.error('Edit error', err);
    alert('Unexpected JS error. Check console for details.');
  });
});

// ----- ATTACH ROW BUTTONS -----
function attachRowButtons(row) {
  // Onboarded
row.querySelector('.onboarded-btn').addEventListener('click', () => {

  const confirmOnboard = confirm("Are you sure you want to Onboard this new joiner?");
    if (!confirmOnboard) return;

    const data = {
        name: row.cells[0].textContent,
        employee_id: row.cells[1].textContent,
        role: row.cells[2].textContent,
        department: row.cells[3].textContent,
        manager_name: row.cells[4].textContent,
        joining_date: row.cells[5].textContent,
        snow_ticket: row.cells[6].textContent,
        pc_type: row.cells[7].textContent
    };
    fetch('onboard_joiner.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ joiner: data })
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            alert(' Successfully onboarded!');
            row.remove();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Unexpected error during onboarding.');
    });
});

  // Cancelled
row.querySelector('.cancelled-btn').addEventListener('click', () => {
    const confirmCancel = confirm("Are you sure you want to cancel this joiner? This will delete the record.");
    if (!confirmCancel) return;

    const employee_id = row.cells[1].textContent; // Assuming employee_id is in the second cell

    fetch('delete_it_onboarding.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ employee_id })
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            alert('Joiner deleted successfully.');
            row.remove();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('An unexpected error occurred.');
    });
});


  // Edit
  row.querySelector('.edit-btn').addEventListener('click', () => {
    const form = document.getElementById('editJoinerForm');
    form.employee_id.value = row.cells[1].textContent;
    form.name.value = row.cells[0].textContent;
    form.role.value = row.cells[2].textContent;
    form.department.value = row.cells[3].textContent;
    form.manager_name.value = row.cells[4].textContent;
    form.joining_date.value = row.cells[5].textContent;
    form.snow_ticket.value = row.cells[6].textContent;
    form.pc_type.value = row.cells[7].textContent;
    form.onboarding_status.value = row.cells[8].textContent;
    document.getElementById('editJoinerModal').style.display = 'block';
  });
}

// ----- INITIAL LOAD -----
window.addEventListener('DOMContentLoaded', loadJoiners);
</script>








</body>
</html>
