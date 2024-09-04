<?php
// Initialize session
session_start();

// Check if the user is logged in and has the role 'normal'
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'normal') {
header("location: ../utils/login.php");
exit;
}

// Include the database connection file
require_once('../utils/config.php');

// Check if project_id is set in the URL
if (!isset($_GET['project_id'])) {
die("Error: project_id is not set.");
}

// Fetch project details based on project_id
$project_id = $_GET['project_id'];
$sql = "SELECT * FROM projects WHERE project_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
$project = $result->fetch_assoc();
} else {
die("Project not found.");
}

$stmt->close();

// Check if the user has already applied for this project
$user_id = $_SESSION["user_id"];
$check_sql = "SELECT * FROM project_registrations WHERE user_id = ? AND project_id = ?";
$check_stmt = $mysqli->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $project_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
// Redirect to project details page if already applied
header("location: project_details.php?project_id=$project_id");
exit;
}

$check_stmt->close();

// Process application form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Insert new application record
$insert_sql = "INSERT INTO project_registrations (user_id, project_id) VALUES (?, ?)";
$insert_stmt = $mysqli->prepare($insert_sql);
$insert_stmt->bind_param("ii", $user_id, $project_id);

if ($insert_stmt->execute()) {
// Mark the user as a team leader for this project
$team_leader_sql = "INSERT INTO team_members (project_id, user_id, is_leader) VALUES (?, ?, 1)";
$team_leader_stmt = $mysqli->prepare($team_leader_sql);
$team_leader_stmt->bind_param("ii", $project_id, $user_id);
$team_leader_stmt->execute();
$team_leader_stmt->close();

// Redirect to project details after successful application
header("location: project_details.php?project_id=$project_id");
exit;
} else {
echo "<p>Error submitting application: " . $mysqli->error . "</p>";
}

$insert_stmt->close();
}

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Apply for Project: <?php echo htmlspecialchars($project['project_name']); ?></title>
<link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file for styling -->
<style>
 .containerr {
max-width: 600px;
margin: 20px auto;
padding: 20px;
border: 1px solid #ccc;
border-radius: 5px;
background-color: #f9f9f9;
}
.form-group {
margin-bottom: 20px;
}
.form-group label {
display: block;
font-weight: bold;
margin-bottom: 5px;
}
.form-group input[type="submit"] {
padding: 10px 15px;
background-color: #007BFF;
color: white;
border: none;
border-radius: 5px;
cursor: pointer;
transition: background-color 0.3s;
}
.form-group input[type="submit"]:hover {
background-color: #0056b3;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="containerr">

<br>
<h2>Apply for Project: <?php echo htmlspecialchars($project['project_name']); ?></h2>
<br>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?project_id=" . $project_id; ?>" method="post">
<div class="form-group">
<input type="submit" value="Apply Now">
</div>
</form>
<p><a href="projects_feed.php">Back to Projects Feed</a></p>
</div>
</body>
</html>
