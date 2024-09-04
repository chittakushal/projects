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

// Fetch volunteer opportunities from the database
$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM volunteer_opportunities WHERE owner_id = ?";
$volunteer_opportunities = [];

if ($stmt = $mysqli->prepare($sql)) {
// Bind variables to the prepared statement as parameters
$stmt->bind_param("i", $user_id);

// Execute the statement
if ($stmt->execute()) {
// Store result
$result = $stmt->get_result();

// Check if there are any volunteer opportunities
if ($result->num_rows > 0) {
// Fetch all volunteer opportunities
$volunteer_opportunities = $result->fetch_all(MYSQLI_ASSOC);
} else {
echo "<p>No volunteer opportunities found.</p>";
}
} else {
echo "<p>Error executing query: " . $mysqli->error . "</p>";
}

// Close statement
$stmt->close();
}

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Volunteer Applications</title>
<link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file for styling -->
<style>
body {
font-family: Arial, sans-serif;
background-color: #f4f4f4;
margin: 0;
padding: 0;
}
h1{
    text-align:center;
}
.containerr {
width: 90%;
margin: 20px auto;
display: flex;
flex-wrap: wrap;
gap: 20px;
}
.card {
width: 300px; /* Fixed width */
height: 400px; /* Fixed height */
background-color: #ffffff;
box-shadow: 0 2px 5px rgba(0,0,0,0.15);
border-radius: 5px;
overflow: hidden;
margin-bottom: 20px;
}
.card img {
width: 100%;
height: 200px; /* Fixed height for cover image */
object-fit: cover;
border-radius: 5px 5px 0 0;
}
.card .content {
padding: 15px;
height: calc(100% - 215px); /* Calculate remaining height for content */
box-sizing: border-box; /* Include padding in height calculation */
overflow: hidden; /* Hide overflow content */
}
.card .content h3 {
margin: 0 0 10px;
font-size: 1.2rem;
}
.card .content p {
margin: 0;
font-size: 1rem;
color: #666666;
/* Limit text to 4 lines and show ellipsis */
display: -webkit-box;
-webkit-line-clamp: 4;
-webkit-box-orient: vertical;
overflow: hidden;
text-overflow: ellipsis;
}
.card .buttons {
display: flex;
justify-content: space-between;
padding: 15px;
box-sizing: border-box;
}
.btns {
padding: 10px 15px;
background-color: #DA7297;
color: white;
border: none;
cursor: pointer;
border-radius: 5px;
text-decoration: none;
text-align: center;
transition: background-color 0.3s;
}
.btns:hover {
background-color: #DA7297;
color:white;
text-decoration:none;
}
.btn-danger {
background-color: #dc3545;
}
.btn-danger:hover {
background-color: #c82333;
}
</style>
</head>

<body>
<?php include('../utils/navbar.php'); ?>

<h1>Manage Volunteer Applications</h1>

<div class="containerr">
<?php if (!empty($volunteer_opportunities)): ?>
<?php foreach ($volunteer_opportunities as $volunteer_opportunity): ?>
<div class="card">
<img src="data:image/jpeg;base64,<?php echo base64_encode($volunteer_opportunity['cover_image']); ?>" alt="Cover Image">
<div class="buttons">
<a href="update_volunteer_opportunity.php?volunteer_id=<?php echo $volunteer_opportunity['volunteer_id']; ?>" class="btns">Update</a>
<a href="delete_volunteer_opportunity.php?volunteer_id=<?php echo $volunteer_opportunity['volunteer_id']; ?>" class="btn btn-danger">Delete</a>
</div>
<div class="content">
<h3><?php echo htmlspecialchars($volunteer_opportunity['opportunity_name']); ?></h3>
<p><?php echo htmlspecialchars($volunteer_opportunity['opportunity_description']); ?></p>
</div>
</div>
<?php endforeach; ?>
<?php else: ?>
<p>No volunteer opportunities found.</p>
<?php endif; ?>
</div>
</body>

</html>
