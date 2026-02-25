<?php
require_once 'includes/db.php';
session_start();

// Get employee ID from URL
$employee_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($employee_id)) {
    header('Location: index.php');
    exit();
}

$conn = getDBConnection();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documents'])) {
    $upload_dir = 'uploads/';
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $files = $_FILES['documents'];
    $file_count = count($files['name']);
    
    // Check current document count
    $count_query = "SELECT COUNT(*) as count FROM employee_documents WHERE employee_id = '" . escapeString($conn, $employee_id) . "'";
    $count_result = $conn->query($count_query);
    $current_count = $count_result->fetch_assoc()['count'];
    
    if ($current_count >= 5) {
        $_SESSION['error_message'] = "Maximum 5 documents allowed";
    } else {
        $success_count = 0;
        
        for ($i = 0; $i < $file_count && ($current_count + $i) < 5; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $file_tmp = $files['tmp_name'][$i];
                $file_name = basename($files['name'][$i]);
                $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Only allow PDF files
                if ($file_type === 'pdf') {
                    $unique_name = time() . '_' . $i . '_' . $file_name;
                    $file_path = $upload_dir . $unique_name;
                    
                    if (move_uploaded_file($file_tmp, $file_path)) {
                        $file_name_escaped = escapeString($conn, $file_name);
                        $file_path_escaped = escapeString($conn, $file_path);
                        $employee_id_escaped = escapeString($conn, $employee_id);
                        
                        $query = "INSERT INTO employee_documents (employee_id, document_name, file_path) 
                                  VALUES ('$employee_id_escaped', '$file_name_escaped', '$file_path_escaped')";
                        
                        if ($conn->query($query)) {
                            $success_count++;
                        }
                    }
                }
            }
        }
        
        if ($success_count > 0) {
            $_SESSION['success_message'] = "$success_count document(s) uploaded successfully";
        }
    }
    
    header("Location: employee-details.php?id=" . urlencode($employee_id));
    exit();
}

// Handle document deletion
if (isset($_GET['delete_doc'])) {
    $doc_id = intval($_GET['delete_doc']);
    
    // Get file path before deleting
    $file_query = "SELECT file_path FROM employee_documents WHERE id = $doc_id AND employee_id = '" . escapeString($conn, $employee_id) . "'";
    $file_result = $conn->query($file_query);
    
    if ($file_result && $file_result->num_rows > 0) {
        $file_row = $file_result->fetch_assoc();
        $file_path = $file_row['file_path'];
        
        // Delete from database
        $delete_query = "DELETE FROM employee_documents WHERE id = $doc_id";
        if ($conn->query($delete_query)) {
            // Delete physical file
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $_SESSION['success_message'] = "Document deleted successfully";
        }
    }
    
    header("Location: employee-details.php?id=" . urlencode($employee_id));
    exit();
}

// Fetch employee data
$employee_id_escaped = escapeString($conn, $employee_id);
$query = "SELECT * FROM employees WHERE id = '$employee_id_escaped'";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    header('Location: index.php');
    exit();
}

$employee = $result->fetch_assoc();

// Fetch employee documents
$docs_query = "SELECT * FROM employee_documents WHERE employee_id = '$employee_id_escaped' ORDER BY upload_date DESC";
$docs_result = $conn->query($docs_query);
$documents = [];
if ($docs_result) {
    while ($row = $docs_result->fetch_assoc()) {
        $documents[] = $row;
    }
}

$doc_count = count($documents);

// Handle success/error messages
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

include 'includes/header.php';
?>

<div class="page-content">
    <!-- Back Button -->
    <div style="margin-bottom: 1.5rem;">
        <a href="index.php" class="btn btn-outline btn-sm">
            <i data-lucide="arrow-left"></i>
            Back to Master List
        </a>
    </div>

    <!-- Header -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 class="page-title"><?php echo htmlspecialchars($employee['name']); ?></h1>
            <p class="page-subtitle"><?php echo htmlspecialchars($employee['position']); ?></p>
        </div>
        <span class="badge badge-<?php echo $employee['status']; ?>">
            <?php echo ucfirst($employee['status']); ?>
        </span>
    </div>

    <?php if ($success_message): ?>
    <div class="alert alert-success">
        <i data-lucide="check-circle" class="alert-icon"></i>
        <span class="alert-text"><?php echo htmlspecialchars($success_message); ?></span>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="alert alert-error">
        <i data-lucide="alert-circle" class="alert-icon"></i>
        <span class="alert-text"><?php echo htmlspecialchars($error_message); ?></span>
    </div>
    <?php endif; ?>

    <!-- Work Information -->
    <div style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <i data-lucide="building-2" style="color: #4f46e5;"></i>
            Work Information
        </h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Employee ID</p>
                    <p class="info-value"><?php echo htmlspecialchars($employee['id']); ?></p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="building-2" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Department</p>
                    <p class="info-value"><?php echo htmlspecialchars($employee['department']); ?></p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Email</p>
                    <p class="info-value"><?php echo htmlspecialchars($employee['email']); ?></p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="phone" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Phone</p>
                    <p class="info-value"><?php echo htmlspecialchars($employee['phone']); ?></p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Date Joined</p>
                    <p class="info-value"><?php echo date('F j, Y', strtotime($employee['date_joined'])); ?></p>
                </div>
            </div>
            <?php if ($employee['transfer_location']): ?>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Transfer Location</p>
                    <p class="info-value"><?php echo htmlspecialchars($employee['transfer_location']); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Personal Information -->
    <div style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <i data-lucide="user" style="color: #4f46e5;"></i>
            Personal Information
        </h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Date of Birth</p>
                    <p class="info-value">
                        <?php echo $employee['date_of_birth'] ? date('F j, Y', strtotime($employee['date_of_birth'])) : 'Not provided'; ?>
                    </p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Address</p>
                    <p class="info-value"><?php echo $employee['address'] ? htmlspecialchars($employee['address']) : 'Not provided'; ?></p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Emergency Contact</p>
                    <p class="info-value"><?php echo $employee['emergency_contact'] ? htmlspecialchars($employee['emergency_contact']) : 'Not provided'; ?></p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i data-lucide="phone" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="info-content">
                    <p class="info-label">Emergency Phone</p>
                    <p class="info-value"><?php echo $employee['emergency_phone'] ? htmlspecialchars($employee['emergency_phone']) : 'Not provided'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Section -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="file-text" style="color: #4f46e5;"></i>
                Documents (<?php echo $doc_count; ?>/5)
            </h3>
            <?php if ($doc_count < 5): ?>
            <label for="file-upload" class="btn btn-primary" style="cursor: pointer;">
                <i data-lucide="upload"></i>
                Upload PDF
            </label>
            <form method="POST" enctype="multipart/form-data" id="uploadForm" style="display: none;">
                <input 
                    type="file" 
                    id="file-upload" 
                    name="documents[]" 
                    accept=".pdf" 
                    multiple 
                    onchange="document.getElementById('uploadForm').submit()"
                >
            </form>
            <?php endif; ?>
        </div>

        <?php if ($doc_count < 5): ?>
        <div class="alert alert-info">
            <i data-lucide="alert-circle" class="alert-icon"></i>
            <span class="alert-text">You can upload up to <?php echo 5 - $doc_count; ?> more PDF document<?php echo (5 - $doc_count) !== 1 ? 's' : ''; ?>.</span>
        </div>
        <?php endif; ?>

        <?php if (count($documents) === 0): ?>
        <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 0.5rem; border: 2px dashed #d1d5db;">
            <i data-lucide="file-text" style="width: 48px; height: 48px; color: #9ca3af; margin: 0 auto 1rem;"></i>
            <p style="color: #6b7280; font-weight: 500; margin-bottom: 0.25rem;">No documents uploaded yet</p>
            <p style="color: #9ca3af; font-size: 0.875rem;">Upload up to 5 PDF documents</p>
        </div>
        <?php else: ?>
        <div class="document-list">
            <?php foreach ($documents as $doc): ?>
            <div class="document-item">
                <div class="document-info">
                    <div class="document-icon">
                        <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div class="document-details">
                        <div class="document-name"><?php echo htmlspecialchars($doc['document_name']); ?></div>
                        <div class="document-date">Uploaded on <?php echo date('m/d/Y', strtotime($doc['upload_date'])); ?></div>
                    </div>
                </div>
                <div class="document-actions">
                    <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" download class="icon-btn icon-btn-blue" title="Download">
                        <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                    </a>
                    <a href="?id=<?php echo urlencode($employee_id); ?>&delete_doc=<?php echo $doc['id']; ?>" 
                       class="icon-btn icon-btn-red" 
                       title="Delete"
                       onclick="return confirm('Are you sure you want to delete this document?')">
                        <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
lucide.createIcons();
</script>

<?php
closeDBConnection($conn);
include 'includes/footer.php';
?>
