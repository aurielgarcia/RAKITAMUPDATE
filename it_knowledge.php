<?php
include('db_connect.php');

// Fetch all resources from the database
$tsql = "SELECT * FROM it_knowledge ORDER BY id DESC";
$stmt = sqlsrv_query($conn, $tsql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Knowledge Based | VERTIV GULF LLC ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        /* Base Layout */
        html, body {
            margin: 0;
            padding: 0;
            overflow-y: auto;
        }
        .directory-table {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .grid-row {
            display: grid;
            grid-template-columns: 1fr 150px 150px 150px;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }

        .grid-row:hover { background: #d7d7d75d; }

        .grid-row.header {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #7f8c8d;
            text-transform: uppercase;
            font-size: 12px;
        }

        .inner-grid {
            display: grid;
            grid-template-columns: 50px 1fr;
            align-items: start;
            gap: 10px;
        }

        .icon-box {
            background: #e74c3c;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 10px 5px;
            border-radius: 4px;
            text-align: center;
        }

        .resource-title {
            display: block;
            font-weight: 600;
            color: #2c3e50;
            font-size: 15px;
        }

        .resource-snippet {
            margin: 4px 0 0 0;
            color: #95a5a6;
            font-size: 13px;
        }

        .col-date {
            color: #7f8c8d;
            font-size: 14px;
            text-align: left;
        }

        .col-action, .col-actiondata {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            text-align: center;
        }

        .col-createdby { text-align: left; }

        /* Updated button style to match your original link design */
        .btn-actionedit, .btn-actionview {
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

        .btn-actionview:hover, .btn-actionedit:hover {
            background: #2c3e50;
            color: white;
        }

        .utility-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .btn-add {
            background-color: #2c3e50;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-add:hover { background-color: #1a252f; }
        .plus-icon { margin-right: 8px; font-size: 18px; }

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
    <a href=""><i class="material-icons">home</i> Main Menu</a>
    <a href="it_knowledge.php" style="background-color: #1a1a1a;"><i class="material-symbols-outlined">cognition_2</i> Knowledge Based</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i> Logout</a>
</div>

<div class="main-content">
    <div class="header-section">
        <h2 class="tab-header">Knowledge Based Directory</h2>
    </div>

    <div class="utility-bar">
        <div class="left-actions">
            <button onclick="openModal()" class="btn-add">
                <span class="plus-icon">+</span> Add New Resource
            </button>
        </div>
        <div class="right-actions">
            <div class="search-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" id="resourceSearch" placeholder="Search by title or description...">
            </div>
        </div>
    </div>
    
    <div class="card table-card">
        <div class="directory-table">
            <div class="grid-row header">
                <div class="col-main">Resource Details</div>
                <div class="col-date">Date Published</div>
                <div class="col-createdby">Created By</div>
                <div class="col-action">Action</div>
            </div>

            <?php 
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { 
                $displayDate = $row['date'] ? $row['date']->format('M d, Y') : 'N/A';
                $rowData = json_encode([
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'createdby' => $row['createdby']
                ]);
            ?>
            <div class="grid-row">
                <div class="col-main">
                    <div class="inner-grid">
                        <div class="icon-box">PDF</div>
                        <div class="text-box">
                            <span class="resource-title"><?php echo htmlspecialchars($row['title']); ?></span>
                            <p class="resource-snippet"><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-date"><?php echo $displayDate; ?></div>
                <div class="col-createdby">
                    <p class="resource-snippet"><?php echo htmlspecialchars($row['createdby']); ?></p>
                </div>
                <div class="col-actiondata">
                    <a href="<?php echo htmlspecialchars($row['pdfurl']); ?>" target="_blank" class="btn-actionview">
                        <span class="material-icons">visibility</span>
                    </a>
                    <button type="button" class="btn-actionedit" 
                            onclick='openEditModal(<?php echo htmlspecialchars($rowData, ENT_QUOTES, "UTF-8"); ?>)'>
                        <span class="material-icons">edit</span>
                    </button>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include('it_knowledge_add.php'); ?>
<?php include('it_knowledge_edit.php'); ?> 
<script>
    function openModal() {
        const modal = document.getElementById('addResourceModal');
        if(modal) { modal.style.display = 'flex'; }
    }

    function closeModal() {
        const modal = document.getElementById('addResourceModal');
        if(modal) { modal.style.display = 'none'; }
    }

    function openEditModal(data) {
        const modal = document.getElementById('editResourceModal');
        if(modal) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_title').value = data.title;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('edit_created_by').value = data.createdby;
            modal.style.display = 'flex';
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('editResourceModal');
        if(modal) { modal.style.display = 'none'; }
    }

    window.onclick = function(event) {
        const addModal = document.getElementById('addResourceModal');
        const editModal = document.getElementById('editResourceModal');
        if (event.target == addModal) { closeModal(); }
        if (event.target == editModal) { closeEditModal(); }
    }

    document.getElementById('resourceSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.grid-row:not(.header)');
        rows.forEach(row => {
            let txt = row.innerText.toLowerCase();
            row.style.display = txt.includes(filter) ? "grid" : "none";
        });
    });
</script>
</body>
</html>