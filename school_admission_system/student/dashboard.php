<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

require_once("../config/db.php");

// Get student details including status
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$_SESSION['student_id']]);
$student = $stmt->fetch();

// Get uploaded documents
$doc_stmt = $conn->prepare("SELECT * FROM documents WHERE student_id = ?");
$doc_stmt->execute([$_SESSION['student_id']]);
$documents = $doc_stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard - School Admission</title>
<link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container">

<h2>Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?>!</h2>

<div class="status-box">
    <h3>Application Status</h3>
    <?php
    $status = $student['status'];
    $status_class = '';
    $status_message = '';
    
    switch($status) {
        case 'pending':
            $status_class = 'status-pending';
            $status_message = 'Your application is under review. Please wait for further updates.';
            break;
        case 'approved':
            $status_class = 'status-approved';
            $status_message = 'Congratulations! Your admission application has been approved!';
            break;
        case 'rejected':
            $status_class = 'status-rejected';
            $status_message = 'Your application was not approved at this time. Please contact the administration.';
            break;
    }
    ?>
    <p class="<?php echo $status_class; ?>">Status: <strong><?php echo strtoupper($status); ?></strong></p>
    <p><?php echo $status_message; ?></p>
</div>

<h3>Your Application Details</h3>
<table class="info-table">
    <tr>
        <td><strong>Full Name:</strong></td>
        <td><?php echo htmlspecialchars($student['fullname']); ?></td>
    </tr>
    <tr>
        <td><strong>Email:</strong></td>
        <td><?php echo htmlspecialchars($student['email']); ?></td>
    </tr>
    <tr>
        <td><strong>Phone:</strong></td>
        <td><?php echo htmlspecialchars($student['phone']); ?></td>
    </tr>
    <tr>
        <td><strong>Date of Birth:</strong></td>
        <td><?php echo htmlspecialchars($student['date_of_birth']); ?></td>
    </tr>
    <tr>
        <td><strong>Address:</strong></td>
        <td><?php echo htmlspecialchars($student['address']); ?></td>
    </tr>
    <tr>
        <td><strong>Parent/Guardian:</strong></td>
        <td><?php echo htmlspecialchars($student['parent_name']); ?></td>
    </tr>
    <tr>
        <td><strong>Parent Phone:</strong></td>
        <td><?php echo htmlspecialchars($student['parent_phone']); ?></td>
    </tr>
    <tr>
        <td><strong>Previous School:</strong></td>
        <td><?php echo htmlspecialchars($student['previous_school']); ?></td>
    </tr>
</table>

<h3>Uploaded Documents</h3>
<?php if (count($documents) > 0): ?>
    <ul>
    <?php foreach ($documents as $doc): ?>
        <li><?php echo htmlspecialchars($doc['document_type']); ?> - <?php echo htmlspecialchars($doc['uploaded_at']); ?></li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No documents uploaded yet.</p>
<?php endif; ?>

<p><a href="upload_document.php" class="btn">Upload Documents</a></p>

<p><a href="logout.php">Logout</a></p>

</div>

</body>
</html>
