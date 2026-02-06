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
    <style>
        /* Base Layout */
        html, body {
            margin: 0;
            padding: 0;
            overflow-y: auto;
        }
        /* Container for the whole table */
        .directory-table {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        /* Defining the main row grid */
        .grid-row {
            display: grid;
            grid-template-columns: 1fr 150px 150px 150px;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }

        .grid-row:hover {
            background: #d7d7d75d;
        }

        .grid-row.header {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #7f8c8d;
            text-transform: uppercase;
            font-size: 12px;
        }

        /* Inner Grid for the Icon + Text alignment */
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

        .col-action {
            text-align: center;
        }
        .col-createdby{
            text-align: left;
        }

        .btn-action {
            display: inline-block;
            padding: 8px 15px;
            border: 1px solid #2c3e50;
            border-radius: 4px;
            text-decoration: none;
            color: #2c3e50;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-action:hover {
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

        .right-actions { display: flex; gap: 10px; }

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
    <a href=""><i class="material-icons">arrow_right</i> IT Knowledge Based</a>
    <a href="#" style="background-color: #1a1a1a;"><i class="material-icons">description</i> Knowledge Based</a>
    <a href="logout.php" class="logout-link"><i class="material-icons">logout</i> Logout</a>
</div>

<div class="main-content">
    <div class="header-section">
        <h2 class="tab-header">Knowledge Base Directory</h2>
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
            // Loop through each database record
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { 
                // Format the date for cleaner display
                $displayDate = $row['date'] ? $row['date']->format('M d, Y') : 'N/A';
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
                <div class="col-action">
                    <a href="<?php echo htmlspecialchars($row['pdfurl']); ?>" target="_blank" class="btn-action">View Details</a>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>

<?php include('it_knowledge_add.php'); ?>

<script>
    function openModal() {
        const modal = document.getElementById('addResourceModal');
        if(modal) { modal.style.display = 'flex'; }
    }

    function closeModal() {
        const modal = document.getElementById('addResourceModal');
        if(modal) { modal.style.display = 'none'; }
    }

    window.onclick = function(event) {
        const modal = document.getElementById('addResourceModal');
        if (event.target == modal) { closeModal(); }
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