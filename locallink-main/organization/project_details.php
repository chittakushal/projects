<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'normal') {
header("location: ../utils/login.php");
exit;
}

require_once('../utils/config.php');

if (!isset($_GET['project_id'])) {
die("Error: project_id is not set.");
}

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

$user_id = $_SESSION['user_id'];
$sql_check_registration = "SELECT registration_id FROM project_registrations WHERE user_id = ? AND project_id = ?";
$stmt_check_registration = $mysqli->prepare($sql_check_registration);
$stmt_check_registration->bind_param("ii", $user_id, $project_id);
$stmt_check_registration->execute();
$result_check_registration = $stmt_check_registration->get_result();

$is_registered = $result_check_registration->num_rows > 0;

$stmt_check_registration->close();

$sql_leaders = "SELECT users.username
FROM team_members
JOIN users ON team_members.user_id = users.user_id
WHERE team_members.project_id = ? AND team_members.is_leader = 1";
$stmt_leaders = $mysqli->prepare($sql_leaders);
$stmt_leaders->bind_param("i", $project_id);
$stmt_leaders->execute();
$result_leaders = $stmt_leaders->get_result();

$leaders = [];
if ($result_leaders->num_rows > 0) {
while ($row = $result_leaders->fetch_assoc()) {
$leaders[] = $row['username'];
}
}

$stmt_leaders->close();

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Project Details: <?php echo htmlspecialchars($project['project_name']); ?></title>
 <style>
.containerr {
max-width: 800px;
margin: 20px auto;
padding: 20px;
border: 1px solid #ccc;
border-radius: 5px;
 }
.project-details {
margin-bottom: 20px;
}
.project-details h2 {
margin-top: 0;
font-size: 24px;
color: #DA7297;

}
.project-details p {
font-size: 16px;
line-height: 1.6;
color: #666;
}
.project-details .leaders {
font-weight: bold;
margin-bottom: 10px;
}
.project-details .leaders ul {
padding-left: 20px;
margin-bottom: 0;
}
.project-details .leaders ul li {
list-style-type: none;
}
.project-details .btn-container {
margin-top: 10px;
}
.project-details .btn {
display: inline-block;
padding: 8px 16px;
background-color: #FFB4C2;
color: #fff;
text-decoration: none;
border-radius: 4px;
transition: background-color 0.3s;
margin-right: 10px;
}
.project-details .btn:hover {
background-color: #DA7297;
}
.project-details .btn-disabled {
background-color: #ccc;
cursor: not-allowed;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="containerr">
<div class="project-details">
<h2><?php echo htmlspecialchars($project['project_name']); ?></h2>
<p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($project['project_description'])); ?></p>
<p><strong>Venue:</strong> <?php echo htmlspecialchars($project['venue']); ?></p>
<p><strong>Current Team Members:</strong> <?php echo $project['current_team_members']; ?></p>
<p><strong>Team Members Limit:</strong> <?php echo $project['team_members_limit'] ?: 'Unlimited'; ?></p>
<p><strong>Project Status:</strong>
<?php
if ($project['is_approved']) {
echo "<span style='color: green;'>Approved</span>";
} elseif ($project['is_disapproved']) {
echo "<span style='color: red;'>Disapproved</span>";
if (!empty($project['disapproval_reason'])) {
echo "<br><strong>Disapproval Reason:</strong><br>" . htmlspecialchars($project['disapproval_reason']);
}
} else {
echo "Pending Approval";
}
?>
</p>
<div class="leaders">
<p><strong>Project Leader(s):</strong></p>
<ul>
<?php foreach ($leaders as $leader): ?>
<li><?php echo htmlspecialchars($leader); ?></li>
<?php endforeach; ?>
</ul>
</div>
<div class="btn-container">
<?php if (!$is_registered): ?>
<a href="apply_project.php?project_id=<?php echo $project_id; ?>" class="btn">Apply for Project</a>
<?php endif; ?>
<?php if ($is_registered): ?>
<a href="view_my_team_members.php?project_id=<?php echo $project_id; ?>" class="btn">View My Team Members</a>
<a href="add_team_members.php?project_id=<?php echo $project_id; ?>" class="btn">Add Team Members</a>
<?php else: ?>
<a href="#" class="btn btn-disabled" disabled>Add Team Members (Apply first)</a>
<?php endif; ?>
</div>
</div>
<p><a href="projects_feed.php">Back to Projects Feed</a></p>
</div>
</body>
</html>
