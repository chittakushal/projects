<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'organization') {
header("location: ../utils/login.php");
exit;
}

require_once('../utils/config.php');

if (!isset($_GET['project_id']) || !isset($_GET['user_id'])) {
die("Error: project_id or user_id is not set.");
}

$project_id = $_GET['project_id'];
$user_id = $_GET['user_id'];

$sql_project = "SELECT project_name FROM projects WHERE project_id = ?";
$stmt_project = $mysqli->prepare($sql_project);
$stmt_project->bind_param("i", $project_id);
$stmt_project->execute();
$result_project = $stmt_project->get_result();

if ($result_project->num_rows > 0) {
$project = $result_project->fetch_assoc();

// Fetch user details
$sql_user = "SELECT username, email FROM users WHERE user_id = ?";
$stmt_user = $mysqli->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
$user = $result_user->fetch_assoc();
} else {
die("User not found.");
}

$stmt_user->close();
} else {
die("Unauthorized access or project not found.");
}

$stmt_project->close();

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Team Members for <?php echo htmlspecialchars($project['project_name']); ?></title>

</head>
<body>
<?php include('../utils/navbar.php'); ?>



<h1>Team Members for <?php echo htmlspecialchars($project['project_name']); ?></h1>
<div class="container">
<div class="card">
<div class="content">
<h3><?php echo htmlspecialchars($user['username']); ?></h3>
<p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
</div>
</div>
</div>
<p><a href="my_projects.php">Back to My Projects</a></p>
</body>
</html>
