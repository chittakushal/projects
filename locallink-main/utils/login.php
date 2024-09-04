<!-- login.php -->

<?php
require_once('config.php');
session_start();

if (isset($_SESSION["user_id"], $_SESSION["username"], $_SESSION["role"])) {
switch ($_SESSION["role"]) {
case 'normal':
header("location: /locallink-main/normal/normal_dashboard.php");
exit();
case 'admin':
header("location: /locallink-main/admin/admin_dashboard.php");
exit();
case 'organization':
header("location: /locallink-main/organization_dashboard.php");
exit();
case 'business':
header("location: /locallink-main/business/business_dashboard.php");
exit();
default:
header("location: /locallink-main/index.php");
exit();
}
}

$username = $password = '';
$username_err = $password_err = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (empty(trim($_POST["username"]))) {
$username_err = "Please enter username.";
} else {
$username = trim($_POST["username"]);
}

if (empty(trim($_POST["password"]))) {
$password_err = "Please enter your password.";
} else {
$password = trim($_POST["password"]);
}

if (empty($username_err) && empty($password_err)) {
$sql = "SELECT user_id, username, password, role FROM users WHERE username = ?";

if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("s", $param_username);
$param_username = $username;

if ($stmt->execute()) {
$stmt->store_result();

if ($stmt->num_rows == 1) {
$stmt->bind_result($user_id, $username, $hashed_password, $role);
if ($stmt->fetch()) {
if (password_verify($password, $hashed_password)) {
session_start();
$_SESSION["user_id"] = $user_id;
$_SESSION["username"] = $username;
$_SESSION["role"] = $role;

switch ($role) {
case 'normal':
header("location: /locallink-main/normal/normal_dashboard.php");
exit();
case 'admin':
header("location: /locallink-main/admin/admin_dashboard.php");
exit();
case 'organization':
header("location: /locallink-main/organization/organization_dashboard.php");
exit();
case 'business':
header("location: /locallink-main/business/business_dashboard.php");
exit();
default:
header("location: /locallink-main/index.php");
exit();
}
} else {
$password_err = "The password you entered was not valid.";
}
}
} else {
$username_err = "No account found with that username.";
}
} else {
echo "Oops! Something went wrong. Please try again later.";
}

$stmt->close();
}
}

$mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<style>
body {
font-family: Arial, sans-serif;
background-color: #667BC6;
display: flex;
justify-content: center;
align-items: center;
height: 100vh;
margin: 0;
}

.wrapper {
max-width: 400px;
width: 100%;
background: rgba(255, 255, 255, 0.1);
backdrop-filter: blur(10px);
padding: 30px;
border-radius: 15px;
box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
border: 1px solid rgba(255, 255, 255, 0.3);
color: #fff;
}

h2 {
text-align: center;
margin-bottom: 20px;
color: white;
}

.form-group {
margin-bottom: 20px;
}

label {
font-weight: bold;
display: block;
margin-bottom: 5px;
}

.form-control {
width: 100%;
padding: 10px;
font-size: 16px;
border: 1px solid #ccc;
border-radius: 4px;
}

.has-error .form-control {
border-color: #FFB4C2;
}

.help-block {
color: #FFB4C2;
font-size: 14px;
}

.btn-primary {
background-color: #DA7297;
color: #fff;
border: none;
padding: 12px 20px;
cursor: pointer;
border-radius: 18px;
width: 100%;

}

.btn-primary:hover {
background-color: #B75D81;
}

p {
text-align: center;
}

a {
color: #FFB4C2;
text-decoration: none;
}

a:hover {
text-decoration: underline;
}
</style>
</head>
<body>
<div class="wrapper">
<h2>Login</h2>
<p>Please fill in your credentials to login.</p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
<label>Username</label>
<input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
<span class="help-block"><?php echo $username_err; ?></span>
</div>    
<div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
<label>Password</label>
<input type="password" name="password" class="form-control">
<span class="help-block"><?php echo $password_err; ?></span>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary" value="Login">
</div>
<p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
</form>
</div>    
</body>
</html>
