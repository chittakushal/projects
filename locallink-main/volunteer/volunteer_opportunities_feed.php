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

// Fetch approved volunteer opportunities from the database
$sql = "SELECT volunteer_id, opportunity_name, opportunity_description, cover_image, applications_count
FROM volunteer_opportunities
WHERE is_disapproved = 0"; // Only fetch opportunities that are approved

$result = $mysqli->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Volunteer Opportunities Feed</title>
<style>
/* Apply the custom color palette */
.wrapper {
max-width: 800px;
margin: 0 auto;
padding: 20px;
color: #333;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
h2 {
color: #667BC6;
text-align: center;
}
.opportunities {
margin-top: 20px;
}
.opportunity-card {
margin-bottom: 20px;
padding: 10px;
border: 1px solid #ccc;
border-radius: 5px;
}
.opportunity-card img {
width: 100%;
max-width: 300px;
height: auto;
border-radius: 5px;
}
.opportunity-card h3 {
margin-top: 10px;
font-size: 20px;
color: #333;
}
.opportunity-card .description {
color: #666;
text-align: justify;
white-space: pre-line; /* Preserve line breaks */
}
.opportunity-card .btn {
display: inline-block;
margin-top: 10px;
padding: 8px 16px;
background-color: #DA7297;
color: #fff;
text-decoration: none;
border-radius: 4px;
transition: background-color 0.3s;
}
.opportunity-card .btn:hover {
background-color: #FFB4C2;
color: #fff;

}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>
<br>
<br>

<br>

<div class="wrapper">
<h2>Volunteer Opportunities</h2>
<br>
<?php if ($result && $result->num_rows > 0): ?>
<div class="opportunities">
<?php while ($row = $result->fetch_assoc()): ?>
<div class="opportunity-card">
<img src="data:image/jpeg;base64,<?php echo base64_encode($row['cover_image']); ?>" alt="Cover Image">
<h3><?php echo htmlspecialchars($row['opportunity_name']); ?></h3>
<p class="description">
<?php
// Split opportunity description into paragraphs
$paragraphs = preg_split('/(?<=[.?!])\s+(?=[a-zA-Z])/', htmlspecialchars($row['opportunity_description']));
foreach ($paragraphs as $paragraph):
?>
<?php echo $paragraph; ?><br>
<?php endforeach; ?>
</p>
<a href="apply_volunteer.php?volunteer_id=<?php echo $row['volunteer_id']; ?>" class="btn">Apply for this Opportunity</a>
</div>
<?php endwhile; ?>
</div>
<?php else: ?>
<p>No volunteer opportunities found.</p>
<?php endif; ?>
</div>
</body>
</html>

<?php
// Close connection
$mysqli->close();
?>
