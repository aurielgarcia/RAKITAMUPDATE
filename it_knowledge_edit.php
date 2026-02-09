<?php
include('db_connect.php');

// Change: Check for 'id' instead to be safer for both AJAX and Form posts
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $createdby = $_POST['created_by'];

    $response = array('success' => false, 'message' => '');

    if (!empty($_FILES['pdf_file']['name'])) {
        // 1. Fetch the OLD file path
        $oldFileSql = "SELECT pdfurl FROM it_knowledge WHERE id = ?";
        $oldFileStmt = sqlsrv_query($conn, $oldFileSql, array($id));
        $oldRow = sqlsrv_fetch_array($oldFileStmt, SQLSRV_FETCH_ASSOC);
        $oldFilePath = $oldRow['pdfurl'] ?? null;

        // 2. Process the NEW file
        $fileName = $_FILES['pdf_file']['name'];
        $uniqueName = time() . "_" . $fileName;
        
        // Use a consistent directory separator
        $uploadDir = "uploads";
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

        $physicalPath = __DIR__ . DIRECTORY_SEPARATOR . $uploadDir . DIRECTORY_SEPARATOR . $uniqueName;
        $dbPath = $uploadDir . "/" . $uniqueName;

        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $physicalPath)) {
            // 3. Update with new path
            $tsql = "UPDATE it_knowledge SET title = ?, description = ?, createdby = ?, pdfurl = ? WHERE id = ?";
            $params = array($title, $description, $createdby, $dbPath, $id);
            
            // 4. Delete the old physical file
            if ($oldFilePath && file_exists(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldFilePath))) {
                unlink(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldFilePath));
            }
        }
    } else {
        // No new file, update text only
        $tsql = "UPDATE it_knowledge SET title = ?, description = ?, createdby = ? WHERE id = ?";
        $params = array($title, $description, $createdby, $id);
    }

    $stmt = sqlsrv_query($conn, $tsql, $params);
    
    if ($stmt) {
        // If it's a standard form post:
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo "<script>window.location.href='it_knowledge.php?edit=success';</script>";
            exit();
        } else {
            // If it's an AJAX call:
            echo json_encode(array('success' => true));
            exit();
        }
    } else {
        echo json_encode(array('success' => false, 'message' => sqlsrv_errors()));
        exit();
    }
}
?>

<div id="editResourceModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 style="margin:0; color:#2c3e50;">Edit Resource</h3>
            <span style="cursor:pointer; font-size:24px; color:#95a5a6;" onclick="closeEditModal()">&times;</span>
        </div>
        
        <form action="it_knowledge_edit.php" method="POST" enctype="multipart/form-data">
            <div class="modal-form">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" id="edit_title" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Replace PDF (Leave blank to keep current)</label>
                    <input type="file" name="pdf_file" accept=".pdf">
                </div>
                <div class="form-group">
                    <label>Created By</label>
                    <select name="created_by" id="edit_created_by" required class="form-select-createdby">
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
                <button class="btn-cancel" type="button" onclick="closeEditModal()">Cancel</button>
                <button type="submit" name="edit_resource" class="btn-save">Update Resource</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* I've kept your professional styles below */
    .modal-overlay {
        display: none; 
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(44, 62, 80, 0.5);
        backdrop-filter: blur(8px);
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
    }
    .modal-header { padding: 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .modal-form { padding: 30px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #7f8c8d; margin-bottom: 8px; }
    .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1.5px solid #dfe6e9; border-radius: 8px; box-sizing: border-box; font-family: inherit; }
    .modal-footer { padding: 20px; background: #f8f9fa; text-align: right; }
    .btn-save { background: #556a80; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .btn-save:hover { background: #2c3e50; }
    .btn-cancel { background: #c36666; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .btn-cancel:hover { background: #de2121; }
    .form-select-createdby {
        width: 100%; padding: 12px; border: 1.5px solid #dfe6e9; border-radius: 8px; background-color: white; color: #2c3e50;
    }
</style>