<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Room Entry Registry | VERTIV GULF LLC ITMS</title>
    
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
        box-sizing: border-box;
        }
        :root {
            --dark-bg: #1e1e1e;
            --text-main: #2c3e50;
            --text-muted: #7f8c8d;
            --border-color: #eef0f2;
            --danger: #e74c3c;
        }

        body, html {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #ffffff;
            color: var(--text-main);
            overflow-y: auto;
        }

        /* Sidebar Styling */
        .sidebar {
        width: 230px;
        height: 100vh;
        background-color: #000;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 20px;
        color: white;
        z-index: 2;
        display: flex;
        flex-direction: column;
        }

        .sidebar .logo {
        width: 90%;
        padding-bottom: 10px;
        padding-right: 20px;

        }

        .sidebar-divider {
        border: none;
        height: 1px;
        background-color: #444;
        /* or any color you prefer */
        margin: 5px;
        }

        .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
        }

        .sidebar a {
        display: flex;
        align-items: flex-start;
        color: white;
        gap: 10px;
        padding: 10px 20px;
        text-decoration: none;
        transition: 0.3s;
        line-height: 1.9;
        }

        .sidebar a:hover {
        background-color: #333;
        }

        .logout-link {
        margin-top: auto;
        background-color: #dc3545;
        color: white;
        padding: 12px 20px;
        text-align: right;
        text-decoration: none;
        }

        .logout-link:hover {
        background-color: #c82333;
        }

        /* Main Content Layout */
        .main-content {
            margin-left: 230px;
            padding: 20px;
            width: calc(100% - 230px);
        }

        /* Header Area */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .tab-header {
        display: inline-block;
        background-color: #343a40;
        color: white;
        padding: 12px 25px;
        font-size: 20px;
        font-weight: bold;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        margin-bottom: 0;
        border: 1px solid #343a40;
        border-bottom: none;
        position: relative;
        top: 1px;
        }

        /* Buttons */
        #newEntryBtn {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #newEntryBtn:hover {
            background: #1a252f;
        }

        /* Table Design */
        .table-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: auto;
            border: 1px solid var(--border-color);
            max-height: calc(100vh - 200px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th {
            background: #f8f9fa;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.8px;
            padding: 18px 20px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 2px solid var(--border-color);
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
            color: #444;
        }

        tr:hover {
            background-color: #fcfcfc;
        }

        .id-badge {
            background: #f1f3f5;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 700;
            color: #7f8c8d;
            font-size: 12px;
        }

        /* Action Buttons */
        .action-container {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 6px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
            font-size: 14px;
        }

        .edit-btn, .delete-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            border: 1px solid #2c3e50;
            border-radius: 4px;
            text-decoration: none;
            color: #2c3e50;
            background: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .edit-btn:hover, .delete-btn:hover {
            background: #2c3e50;
            color: white;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            position: relative;
        }

        .modal-content h2 {
            margin-top: 0;
            font-weight: 700;
            text-align: left;
            color: var(--text-main);
        }

        .close {
            position: absolute;
            right: 25px;
            top: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #aaa;
        }

        .modal label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .modal input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #dcdde1;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .modal input:focus {
            outline: none;
            border-color: #2c3e50;
            box-shadow: 0 0 0 3px rgba(210, 105, 30, 0.1);
        }

        .save-btn {
            background: #556a80;
            color: white;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        .save-btn:hover {
            background: #2c3e50;
        }

        .utility-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            color: #95a5a6;
            font-size: 14px;
        }

        #resourceSearch {
            padding: 11px 15px 11px 40px;
            border: 2px solid #dcdde1;
            border-radius: 8px;
            width: 300px;
            outline: none;
            transition: border-color 0.3s;
        }

        #resourceSearch:focus { border-color: #3498db; }

    </style>
</head>
<body>

<div class="sidebar">
    <img src="images/vertiv-logo1.png" alt="Vertiv Logo" class="logo">
    <hr class="sidebar-divider">
    <a href="main_menu.php"><i class="material-icons">home</i> Main Menu</a>
    <a href="server_room_entry.php" style="background-color: #1a1a1a;"><i class="material-icons">arrow_right</i> Server Room Entry</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i> Logout</a>
</div>

<div class="main-content">
    <div class="header-section">
        <div class="tab-header">Server Room Entry Registry</div>
    </div>
    
    <div class="utility-bar">
        <div class="left-actions">
            <button id="newEntryBtn"><i class="fas fa-plus"></i> New Entry</button>
        </div>

        <div class="right-actions">
            <div class="search-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" id="resourceSearch" placeholder="Search by Name or Designation...">
            </div>
        </div>
    </div>

    <div class="table-wrapper">
        <table id="stagingTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Name of Person</th>
                    <th>Designation</th>
                    <th>Reason</th>
                    <th>Authorized By</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>
</div>

<div id="newEntryModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('newEntryModal')">&times;</span>
    <h2>Add New Entry</h2>
    <form id="newEntryForm">
      <label>Date</label>
      <input type="date" name="entry_date" required>

      <div style="display: flex; gap: 15px;">
          <div style="flex:1">
            <label>Time-in</label>
            <input type="time" name="entry_time" required>
          </div>
          <div style="flex:1">
            <label>Time-out</label>
            <input type="time" name="exit_time" required>
          </div>
      </div>

      <label>Name of Person Entering</label>
      <input type="text" name="visitor_name" placeholder="Full Name" required>

      <label>Designation / Company</label>
      <input type="text" name="department_company" placeholder="e.g. IT Dept" required>

      <label>Reason for Entry</label>
      <input type="text" name="purpose" placeholder="e.g. Server Maintenance" required>

      <label>Authorized By</label>
      <input type="text" name="authorized_by" placeholder="Supervisor Name" required>

      <button type="submit" class="save-btn">Save Entry</button>
    </form>
  </div>
</div>

<div id="editEntryModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('editEntryModal')">&times;</span>
    <h2>Edit Entry</h2>
    <form id="editEntryForm">
      <input type="hidden" name="id">
      
      <label>Date</label>
      <input type="date" name="entry_date" required>

      <div style="display: flex; gap: 15px;">
          <div style="flex:1">
            <label>Time-in</label>
            <input type="time" name="entry_time" required>
          </div>
          <div style="flex:1">
            <label>Time-out</label>
            <input type="time" name="exit_time" required>
          </div>
      </div>

      <label>Name of Person Entering</label>
      <input type="text" name="visitor_name" required>

      <label>Designation</label>
      <input type="text" name="department_company" required>

      <label>Reason for Entry</label>
      <input type="text" name="purpose" required>

      <label>Authorized By</label>
      <input type="text" name="authorized_by" required>

      <button type="submit" class="save-btn">Update Entry</button>
    </form>
  </div>
</div>

<script>
const tbody = document.querySelector('#stagingTable tbody');

// Helper Functions
function formatTime12h(timeStr) {
    if (!timeStr) return '';
    if (timeStr.toLowerCase().includes('am') || timeStr.toLowerCase().includes('pm')) return timeStr;
    const [hours, minutes] = timeStr.split(':');
    let h = parseInt(hours);
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return `${h}:${minutes} ${ampm}`;
}

function convertTo24Hour(time12h) {
    if(!time12h) return '';
    if (!time12h.toLowerCase().includes('am') && !time12h.toLowerCase().includes('pm')) return time12h;
    const [time, modifier] = time12h.split(' ');
    let [hours, minutes] = time.split(':');
    if (hours === '12') hours = '00';
    if (modifier === 'PM') hours = parseInt(hours, 10) + 12;
    return `${hours.toString().padStart(2, '0')}:${minutes}`;
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Event Listeners
document.getElementById('newEntryBtn').addEventListener('click', () => {
    document.getElementById('newEntryForm').reset();
    document.getElementById('newEntryModal').style.display = 'block';
});

window.onclick = e => {
    if (e.target.classList.contains('modal')) e.target.style.display = 'none';
};

// Data Logic
function createTableRow(data, index) {
    const row = document.createElement('tr');
    row.dataset.id = data.id; 

    row.innerHTML = `
        <td><span class="id-badge">${index + 1}</span></td>
        <td><strong>${data.entry_date}</strong></td>
        <td>${formatTime12h(data.entry_time)}</td>
        <td>${formatTime12h(data.exit_time)}</td>
        <td style="font-weight:500; color:var(--text-main)">${data.visitor_name}</td>
        <td>${data.department_company}</td>
        <td>${data.purpose}</td>
        <td>${data.authorized_by}</td>
        <td>
            <div class="action-container">
                <button class="action-btn edit-btn" title="Edit"><i class="fas fa-edit"></i></button>    
                <button class="action-btn delete-btn" title="Delete"><i class="fas fa-trash"></i></button>
            </div>
        </td>
    `;
    attachRowButtons(row);
    return row;
}

function loadEntries() {
    fetch('get_server_entry.php')
    .then(res => res.json())
    .then(data => {
        tbody.innerHTML = '';
        if (Array.isArray(data)) {
            data.forEach((item, index) => {
                tbody.appendChild(createTableRow(item, index));
            });
        }
    })
    .catch(err => console.error(err));
}

function attachRowButtons(row) {
    row.querySelector('.delete-btn').addEventListener('click', () => {
        if (!confirm("Are you sure you want to delete this entry?")) return;
        fetch('delete_server_entry.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ entry_id: row.dataset.id })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) { loadEntries(); } 
            else alert('Error: ' + result.message);
        });
    });

    row.querySelector('.edit-btn').addEventListener('click', () => {
        const form = document.getElementById('editEntryForm');
        form.id.value = row.dataset.id;
        form.entry_date.value = row.cells[1].textContent;
        form.entry_time.value = convertTo24Hour(row.cells[2].textContent);
        form.exit_time.value = convertTo24Hour(row.cells[3].textContent);
        form.visitor_name.value = row.cells[4].textContent;
        form.department_company.value = row.cells[5].textContent;
        form.purpose.value = row.cells[6].textContent;
        form.authorized_by.value = row.cells[7].textContent;
        document.getElementById('editEntryModal').style.display = 'block';
    });
}

// Form Submissions
document.getElementById('newEntryForm').addEventListener('submit', e => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    fetch('add_server_entry.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ entries: [data] })
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            closeModal('newEntryModal');
            loadEntries();
        }
    });
});

document.getElementById('editEntryForm').addEventListener('submit', e => {
    e.preventDefault();
    const entryData = Object.fromEntries(new FormData(e.target));
    fetch('edit_server_entry.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ entry: entryData })
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            closeModal('editEntryModal');
            loadEntries();
        }
    });
});

loadEntries();

    document.getElementById('resourceSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#stagingTable tbody tr');
        rows.forEach(row => {
            let txt = row.innerText.toLowerCase();
            row.style.display = txt.includes(filter) ? "" : "none";
        });
    });

</script>
</body>
</html>