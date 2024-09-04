<?php
// Initialize session
session_start();

// Check if the user is logged in and has the role 'organization'
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'organization') {
header("location: ../utils/login.php");
exit;
}

// Include the database connection file
require_once('../utils/config.php');

// Fetch all projects and their applicants (including team members)
$sql_projects = "
SELECT p.project_id, p.project_name, COUNT(tm.user_id) AS team_members_count
FROM projects p
LEFT JOIN team_members tm ON p.project_id = tm.project_id
WHERE p.is_disapproved = 0
GROUP BY p.project_id, p.project_name";
$result_projects = $mysqli->query($sql_projects);

$projects = [];
if ($result_projects->num_rows > 0) {
while ($row = $result_projects->fetch_assoc()) {
// Fetch team members for each project
$project_id = $row['project_id'];
$sql_team_members = "
SELECT u.username, u.email, tm.is_leader
FROM team_members tm
JOIN users u ON tm.user_id = u.user_id
WHERE tm.project_id = ?";
$stmt_team_members = $mysqli->prepare($sql_team_members);
$stmt_team_members->bind_param("i", $project_id);
$stmt_team_members->execute();
$team_members_result = $stmt_team_members->get_result();

$team_members = [];
if ($team_members_result->num_rows > 0) {
while ($member_row = $team_members_result->fetch_assoc()) {
$team_members[] = $member_row;
}
}

$stmt_team_members->close();

// Add project details with team members to the projects array
$row['team_members'] = $team_members;
$projects[] = $row;
}
}

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Projects and Team Members</title>
<link rel="stylesheet" href="../styles.css">
<style>
body {
font-family: Arial, sans-serif;
background-color: #f0f2f5;
margin: 0;
padding: 0;
}

.containerr {
max-width: 900px;
margin: 40px auto;
padding: 20px;
background-color: #ffffff;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
border-radius: 8px;
}

h1 {
font-size: 24px;
color: #333;
margin-bottom: 20px;
text-align: center;
}

.project-card {
margin-bottom: 20px;
padding: 20px;
border: 1px solid #ddd;
border-radius: 8px;
background-color: #fafafa;
box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.project-card h3 {
margin-top: 0;
font-size: 22px;
color: #444;
}

.project-card p {
margin: 10px 0;
color: #666;
}

.project-card .team-members {
font-weight: bold;
margin-bottom: 10px;
}

.project-card .team-members ul {
padding-left: 20px;
margin-bottom: 0;
list-style-type: none;
}

.project-card .team-members ul li {
margin-bottom: 5px;
}

.project-card .team-members ul li span {
color: #555;
}

.project-card .team-members ul li .leader {
font-weight: bold;
color: #DA7297;
}

a {
display: inline-block;
margin-top: 20px;
color: #667BC6;
text-decoration: none;
}

a:hover {
text-decoration: underline;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="containerr">
<h1>All Projects and Team Members</h1>
<?php if (!empty($projects)): ?>
<?php foreach ($projects as $project): ?>
<div class="project-card">
<h3><?php echo htmlspecialchars($project['project_name']); ?></h3>
<p><strong>Team Members:</strong> <?php echo $project['team_members_count']; ?></p>
<?php if (!empty($project['team_members'])): ?>
<div class="team-members">
<p><strong>Team Members:</strong></p>
<ul>
<?php foreach ($project['team_members'] as $member): ?>
<li>
<span><?php echo htmlspecialchars($member['username']); ?></span> 
(<?php echo htmlspecialchars($member['email']); ?>)
<?php if ($member['is_leader']): ?>
- <span class="leader">Team Leader</span>
<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
</div>
<?php else: ?>
<p>No team members found for this project.</p>
<?php endif; ?>
</div>
<?php endforeach; ?>
<?php else: ?>
<p>No projects found.</p>
<?php endif; ?>
<p><a href="my_projects.php">Back to My Projects</a></p>
</div>
</body>
</html>
