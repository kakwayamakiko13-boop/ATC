<?php
require_once("../config/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize all inputs
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));
    $date_of_birth = $_POST['date_of_birth'];
    $parent_name = htmlspecialchars(trim($_POST['parent_name']));
    $parent_phone = htmlspecialchars(trim($_POST['parent_phone']));
    $previous_school = htmlspecialchars(trim($_POST['previous_school']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Validate password strength
    if (strlen($_POST['password']) < 6) {
        $message = "Password must be at least 6 characters";
    } else {
        try {
            // Check if email already exists
            $check_stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
            $check_stmt->execute([$email]);
            
            if ($check_stmt->rowCount() > 0) {
                $message = "Email already registered";
            } else {
                // Insert with all application fields
                $stmt = $conn->prepare("INSERT INTO students(fullname, email, phone, address, date_of_birth, parent_name, parent_phone, previous_school, password, status) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
                
                if ($stmt->execute([$fullname, $email, $phone, $address, $date_of_birth, $parent_name, $parent_phone, $previous_school, $password])) {
                    $message = "Registration successful! Your application status is pending. You can login now.";
                } else {
                    $message = "Error registering user.";
                }
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Register - School Admission</title>
<link rel="stylesheet" href="../css/style.css">
<script src="../js/script.js"></script>
</head>
<body>

<div class="container">
<h2>Student Admission Application</h2>

<p><?php echo $message; ?></p>

<form method="POST" onsubmit="return validateRegister()">

<h3>Personal Information</h3>
<input type="text" name="fullname" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="tel" name="phone" placeholder="Phone Number" required>
<input type="date" name="date_of_birth" placeholder="Date of Birth" required>
<textarea name="address" placeholder="Address" rows="2" required></textarea>

<h3>Parent/Guardian Information</h3>
<input type="text" name="parent_name" placeholder="Parent/Guardian Name" required>
<input type="tel" name="parent_phone" placeholder="Parent/Guardian Phone" required>

<h3>Education Background</h3>
<input type="text" name="previous_school" placeholder="Previous School Name" required>

<h3>Account Information</h3>
<input type="password" id="password" name="password" placeholder="Password (min 6 characters)" required>
<input type="password" id="confirm" placeholder="Confirm Password" required>

<button type="submit">Submit Application</button>

</form>

<a href="login.php">Already have account? Login</a>
</div>

</body>
</html>
