<?php
session_start();
require_once("../config/db.php");

$message="";

if($_SERVER["REQUEST_METHOD"]=="POST"){

$username=$_POST['username'];
$password=$_POST['password'];

$stmt=$conn->prepare("SELECT * FROM admin WHERE username=?");
$stmt->execute([$username]);

$admin=$stmt->fetch();

if($admin && password_verify($password,$admin['password'])){

$_SESSION['admin']=$admin['username'];

header("Location: dashboard.php");
exit;

}else{
$message="Invalid admin login";
}

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container">

<h2>Admin Login</h2>

<p><?php echo $message;?></p>

<form method="POST">

<input type="text" name="username" placeholder="Username" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Login</button>

</form>

</div>

</body>
</html>