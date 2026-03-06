<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$stmt = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
$students = $stmt->fetchAll();

// Get statistics
$total_stmt = $conn->query("SELECT COUNT(*) as total FROM students");
$total_students = $total_stmt->fetch()['total'];

$pending_stmt = $conn->query("SELECT COUNT(*) as pending FROM students WHERE status = 'pending'");
$pending_count = $pending_stmt->fetch()['pending'];

$approved_stmt = $conn->query("SELECT COUNT(*) as approved FROM students WHERE status = 'approved'");
$approved_count = $approved_stmt->fetch()['approved'];

$rejected_stmt = $conn->query("SELECT COUNT(*) as rejected FROM students WHERE status = 'rejected'");
$rejected_count = $rejected_stmt->fetch()['rejected'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard - School Admission</title>
<link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container">

<h2>Admin Dashboard</h2>

<div class="stats">
    <div class="stat-box">
        <h3><?php echo $total_students; ?></h3>
        <p>Total Applications</p>
    </div>
    <div class="stat-box stat-pending">
        <h3><?php echo $pending_count; ?></h3>
        <p>Pending</p>
    </div>
    <div class="stat-box stat-approved">
        <h3><?php echo $approved_count; ?></h3>
        <p>Approved</p>
    </div>
    <div class="stat-box stat-rejected">
        <h3><?php echo $rejected_count; ?></h3>
        <p>Rejected</p>
    </div>
</div>

<h3>All Applications</h3>

<table border="1" width="100%">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Previous School</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php foreach ($students as $row): ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
    <td><?php echo htmlspecialchars($row['email']); ?></td>
    <td><?php echo htmlspecialchars($row['phone']); ?></td>
    <td><?php echo htmlspecialchars($row['previous_school']); ?></td>
    <td>
        <?php 
        $status_class = 'status-' . $row['status'];
        echo '<span class="' . $status_class . '">' . strtoupper($row['status']) . '</span>'; 
        ?>
    </td>
    <td>
        <?php if ($row['status'] == 'pending'): ?>
            <a href="update_status.php?id=<?php echo $row['id']; ?>&status=approved" onclick="return confirm('Approve this application?')">Approve</a>
            <a href="update_status.php?id=<?php echo $row['id']; ?>&status=rejected" onclick="return confirm('Reject this application?')">Reject</a>
        <?php elseif ($row['status'] == 'approved'): ?>
            <a href="update_status.php?id=<?php echo $row['id']; ?>&status=rejected" onclick="return confirm('Reject this application?')">Reject</a>
        <?php elseif ($row['status'] == 'rejected'): ?>
            <a href="update_status.php?id=<?php echo $row['id']; ?>&status=approved" onclick="return confirm('Approve this application?')">Approve</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>

</table>

<br>

<a href="logout.php">Logout</a>

</div>

</body>
</html>
