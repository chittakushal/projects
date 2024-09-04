<?php
require_once('config.php');

$username = $password = $confirm_password = $email = $role = '';
$username_err = $password_err = $confirm_password_err = $email_err = $role_err = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

if (empty(trim($_POST["username"]))) {
$username_err = "Please enter a username.";
} else {
$sql = "SELECT user_id FROM users WHERE username = ?";

if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("s", $param_username);
$param_username = trim($_POST["username"]);

if ($stmt->execute()) {
$stmt->store_result();

if ($stmt->num_rows == 1) {
$username_err = "This username is already taken.";
} else {
$username = trim($_POST["username"]);
}
} else {
echo "Oops! Something went wrong. Please try again later.";
}

$stmt->close();
}
}

if (empty(trim($_POST["password"]))) {
$password_err = "Please enter a password.";     
} elseif (strlen(trim($_POST["password"])) < 6) {
$password_err = "Password must have at least 6 characters.";
} else {
$password = trim($_POST["password"]);
}

if (empty(trim($_POST["confirm_password"]))) {
$confirm_password_err = "Please confirm password.";     
} else {
$confirm_password = trim($_POST["confirm_password"]);
if ($password != $confirm_password) {
$confirm_password_err = "Password did not match.";
}
}

if (empty(trim($_POST["email"]))) {
$email_err = "Please enter an email address.";
} else {
$sql = "SELECT user_id FROM users WHERE email = ?";

if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("s", $param_email);
$param_email = trim($_POST["email"]);

if ($stmt->execute()) {
$stmt->store_result();

if ($stmt->num_rows == 1) {
$email_err = "This email is already registered.";
} else {
$email = trim($_POST["email"]);
}
} else {
echo "Oops! Something went wrong. Please try again later.";
}

$stmt->close();
}
}

if (empty(trim($_POST["role"]))) {
$role_err = "Please select a role.";
} else {
$role = trim($_POST["role"]);
}

if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err) && empty($role_err)) {

$sql = "INSERT INTO users (username, password, role, email) VALUES (?, ?, ?, ?)";

if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("ssss", $param_username, $param_password, $param_role, $param_email);
$param_username = $username;
$param_password = password_hash($password, PASSWORD_DEFAULT);
$param_role = $role;
$param_email = $email;

if ($stmt->execute()) {
header("location: login.php");
exit();
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
<title>User Registration</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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
max-width: 500px;
width: 100%;
background: rgba(255, 255, 255, 0.25);
border-radius: 16px;
backdrop-filter: blur(10px);
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
padding: 20px;
box-sizing: border-box;
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
color: white;
}

.form-control {
width: 100%;
padding: 10px;
font-size: 16px;
border: 1px solid #ccc;
border-radius: 4px;
box-sizing: border-box;
}

.has-error .form-control {
border-color: #d9534f;
}

.help-block {
color: #d9534f;
font-size: 14px;
}

.btn-primary {
background-color: #667BC6;
color: #fff;
border: none;
padding: 10px 20px;
cursor: pointer;
border-radius: 4px;
margin-right: 10px;
}

.btn-primary:hover {
background-color: #4a5a94;
}

.btn-default {
background-color: #FFB4C2;
color: #333;
border: none;
padding: 10px 20px;
cursor: pointer;
border-radius: 4px;
}

.btn-default:hover {
background-color: #d394a1;
}
</style>
</head>
<body>
<div class="wrapper">
<h2>Sign Up</h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
<label>Username</label>
<input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
<span class="help-block"><?php echo $username_err; ?></span>
</div>    
<div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
<label>Password</label>
<input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
<span class="help-block"><?php echo $password_err; ?></span>
</div>
<div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
<label>Confirm Password</label>
<input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
<span class="help-block"><?php echo $confirm_password_err; ?></span>
</div>
<div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
<label>Email</label>
<input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
<span class="help-block"><?php echo $email_err; ?></span>
</div>
<div class="form-group <?php echo (!empty($role_err)) ? 'has-error' : ''; ?>">
<label>Role</label>
<select name="role" class="form-control">
<option value="">Please select a role</option>
<option value="normal" <?php if ($role == 'normal') echo 'selected'; ?>>Member</option>
<option value="organization" <?php if ($role == 'organization') echo 'selected'; ?>>Organization</option>
<option value="business" <?php if ($role == 'business') echo 'selected'; ?>>Business</option>
</select>
<span class="help-block"><?php echo $role_err; ?></span>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary" value="Register">
<input type="reset" class="btn btn-default" value="Reset">
</div>
<p>Already have an account? <a href="login.php">Login here</a>.</p>
</form>
</div>    
</body>
</html>
