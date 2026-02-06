<?php
include('db_connect.php');

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date']; 
    $createdby = $_POST['created_by'];
    
    // 1. Setup File Info
    $fileName = $_FILES['pdf_file']['name'];
    $fileTmpName = $_FILES['pdf_file']['tmp_name'];
    
    // Use a timestamp to prevent files with the same name from overwriting each other
    $uniqueName = time() . "_" . basename($fileName);
    
    // Absolute path for Windows/XAMPP
    $physicalPath = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $uniqueName;
    $dbRelativePath = "uploads/" . $uniqueName;

    // Ensure directory exists
    if (!is_dir(__DIR__ . DIRECTORY_SEPARATOR . "uploads")) {
        mkdir(__DIR__ . DIRECTORY_SEPARATOR . "uploads", 0777, true);
    }

    // 2. Move the actual file from temporary memory to your 'uploads' folder
    if (move_uploaded_file($fileTmpName, $physicalPath)) {
        
        // 3. Save ONLY the path (string) to the database
        $tsql = "INSERT INTO it_knowledge (title, description, date, pdfurl, createdby) VALUES (?, ?, ?, ?, ?)";
        
        // We save $uploadDirectory (the string "uploads/12345_file.pdf") into the pdfurl column
        $params = array($title, $description, $date, $dbRelativePath, $createdby);
        
        $stmt = sqlsrv_query($conn, $tsql, $params);
        
        if ($stmt) {
            header("Location: it_knowledge.php?upload=success");
            exit();
        } else {
            die(print_r(sqlsrv_errors(), true));
        }
    } else {
        // This triggers if the 'uploads' folder is missing or not writable
        echo "<script>alert('Error: Could not save the file to the code folder. Please ensure the uploads folder exists.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Resource | VERTIV ITMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    /* Professional Blurry Overlay */
    .modal-overlay {
        display: none; 
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(44, 62, 80, 0.5); /* Semi-transparent navy */
        backdrop-filter: blur(8px); /* MODERN BLUR EFFECT */
        z-index: 2000;
        align-items: center;
        justify-content: center;
    }

    .modal-container {
        background: white;
        width: 500px;
        border-radius: 16px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header { padding: 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .modal-form { padding: 30px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #7f8c8d; margin-bottom: 8px; }
    .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1.5px solid #dfe6e9; border-radius: 8px; box-sizing: border-box; font-family: inherit; }
    
    .modal-footer { padding: 20px; background: #f8f9fa; text-align: right; }
    .btn-save { background: #556a80; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .btn-save:hover {
        background: #2c3e50;
        color: white;
    }

    .btn-cancel{
       background: #c36666; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; 
    }

    .btn-cancel:hover {
        background: #de2121;
        color: white;
    }   

    .form-select-createdby {
    width: 100%;
    padding: 12px;
    border: 1.5px solid #dfe6e9;
    border-radius: 8px;
    background-color: white;
    font-family: inherit;
    color: #2c3e50;
    cursor: pointer;
    appearance: none; /* Removes default browser arrow */
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1em;
    }

    .form-select-createdby:focus {
        border-color: #2c3e50;
        outline: none;
        box-shadow: 0 0 0 4px rgba(44, 62, 80, 0.05);
    }



    </style>
</head>
<body>

<div id="addResourceModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 style="margin:0; color:#2c3e50;">Add New Resource</h3>
            <span style="cursor:pointer; font-size:24px; color:#95a5a6;" onclick="closeModal()">&times;</span>
        </div>
        
        <form action="it_knowledge_add.php" method="POST" enctype="multipart/form-data">
            <div class="modal-form">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>PDF</label>
                        <input type="file" name="pdf_file" accept=".pdf">
                    </div>
                    <div class="form-group">
                        <label>Created By</label>
                    <select name="created_by" required class="form-select-createdby">
                        <option value="" disabled selected>Select Author</option>
                        <option value="Therrence Serac Catalan">Therrence Serac Catalan</option>
                        <option value="Gilbert Bautista">Gilbert Bautista</option>
                        <option value="Auriel Garcia">Auriel Garcia</option>
                        <option value="Julieben Labrada">Julieben Labrada</option>
                        <option value="Ralph Carlo Sevilla">Ralph Carlo Sevilla</option>
                    </select>
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" type="button" onclick="closeModal()">Cancel</button>
                <button type="submit" name="submit" class="btn-save">Publish Resource</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>