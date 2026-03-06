<?php
session_start();
require_once("../config/db.php");

$message = "";

// If already logged in, redirect to dashboard
if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    if (!$email) {
        $message = "Invalid email format";
    } else {
        $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['student_id'] = $user['id'];
            $_SESSION['student_name'] = $user['fullname'];
            $_SESSION['student_status'] = $user['status'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Login - School Admission</title>
<link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container small">

<h2>Student Login</h2>

<?php if ($message): ?>
<p style="color: red; text-align: center;"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST">

<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>

<button type="submit">Login</button>

</form>

<a href="register.php">Register New Application</a>

</div>

</body>
</html>
