<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once "../config/db.php";

// Validate and sanitize input
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    die("Invalid request");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);

// Validate status value
$allowed_statuses = ['pending', 'approved', 'rejected'];
if (!$id || !in_array($status, $allowed_statuses)) {
    die("Invalid parameters");
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("UPDATE students SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

header("Location: admin_dashboard.php");
exit;
?>
