<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

require_once("../config/db.php");

$message = "";
$success = false;

// Allowed file types
$allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $message = "Please select a file to upload";
    } else {
        $document_type = htmlspecialchars($_POST['document_type']);
        $file = $_FILES['document'];
        
        // Validate file type
        if (!in_array($file['type'], $allowed_types)) {
            $message = "Invalid file type. Allowed: PDF, JPEG, PNG";
        } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            $message = "File size exceeds 5MB limit";
        } else {
            // Create uploads directory if not exists
            $upload_dir = "../uploads/" . $_SESSION['student_id'] . "/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = $document_type . "_" . time() . "." . $file_ext;
            $file_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Save to database
                $stmt = $conn->prepare("INSERT INTO documents (student_id, document_type, file_path) VALUES (?, ?, ?)");
                if ($stmt->execute([$_SESSION['student_id'], $document_type, $file_path])) {
                    $message = "Document uploaded successfully!";
                    $success = true;
                } else {
                    $message = "Error saving document to database";
                }
            } else {
                $message = "Error uploading file";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Upload Document - School Admission</title>
<link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container small">

<h2>Upload Documents</h2>

<?php if ($message): ?>
<p style="color: <?php echo $success ? 'green' : 'red'; ?>; text-align: center;">
    <?php echo htmlspecialchars($message); ?>
</p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<label for="document_type">Document Type:</label>
<select name="document_type" id="document_type" required>
    <option value="">Select Document Type</option>
    <option value="Birth Certificate">Birth Certificate</option>
    <option value="Photo ID">Photo ID</option>
    <option value="Previous Marksheet">Previous Marksheet</option>
    <option value="Transfer Certificate">Transfer Certificate</option>
    <option value="Other">Other</option>
</select>

<label for="document">Select File (PDF, JPEG, PNG - Max 5MB):</label>
<input type="file" name="document" id="document" accept=".pdf,.jpeg,.jpg,.png" required>

<button type="submit">Upload Document</button>

</form>

<p style="text-align: center;">
    <a href="dashboard.php">Back to Dashboard</a>
</p>

</div>

</body>
</html>
