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

// Fetch approved projects from the database along with team members count
$sql = "SELECT p.*, COUNT(tm.team_member_id) AS team_members_count
FROM projects p
LEFT JOIN team_members tm ON p.project_id = tm.project_id
WHERE p.is_disapproved = 0
GROUP BY p.project_id";

$result = $mysqli->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Projects Feed</title>
<style>
 .wrapper {
max-width: 800px;
margin: 0 auto;
padding: 20px;
}
.project-card {
margin-bottom: 20px;
padding: 10px;
border: 1px solid #ccc;
border-radius: 5px;
}
.project-card img {
width: 100%;
max-width: 300px;
height: 300px;
border-radius: 5px;
}
.project-card h3 {
margin-top: 10px;
font-size: 20px;
color: #DA7297;
 }
.project-card p {
color: #666;
text-align: justify;  
}
.project-card .btn {
display: inline-block;
margin-top: 10px;
padding: 8px 16px;
background-color: #FFB4C2;
color: #fff;
text-decoration: none;
border-radius: 4px;
transition: background-color 0.3s;
}
.project-card .btn:hover {
background-color: #DA7297;
color: #fff;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="wrapper">
<h2>Projects Feed</h2>
<?php if ($result->num_rows > 0): ?>
<div class="projects">
<?php while ($row = $result->fetch_assoc()): ?>
<div class="project-card">
<img src="data:image/jpeg;base64,<?php echo base64_encode($row['cover_image']); ?>" alt="Cover Image">
<h3><?php echo htmlspecialchars($row['project_name']); ?></h3>
<p><?php echo htmlspecialchars($row['project_description']); ?></p>
<p><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
<p><strong>Current Team Members:</strong> <?php echo $row['team_members_count']; ?></p>
<a href="apply_project.php?project_id=<?php echo $row['project_id']; ?>" class="btn">Apply for this Project</a>
<a href="project_details.php?project_id=<?php echo $row['project_id']; ?>" class="btn">View Details</a>
</div>
<?php endwhile; ?>
</div>
<?php else: ?>
<p>No projects found.</p>
<?php endif; ?>
</div>
</body>
</html>

<?php
// Close connection
$mysqli->close();
?>
