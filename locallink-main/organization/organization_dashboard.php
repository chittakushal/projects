<?php
// Initialize session
session_start();

// Check if the user is logged in and has the role 'organization'
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'organization') {
header("location: ../utils/login.php");
exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organization Dashboard</title>

<style>
body {
font-family: 'Arial', sans-serif;
background-color: #667BC6;
margin: 0;
padding: 0;
box-sizing: border-box;

}

.containerr {
max-width: 1200px;
margin: 20px auto;
background-color: #ffffff;
padding: 20px;
border-radius: 8px;
box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

h1 {
font-size: 2.5rem;
color: #DA7297;
text-align: center;
margin-bottom: 20px;
}

.dashboard-sections {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
gap: 30px;
margin-top: 30px;
}

.section {
background-color: #667BC6;
padding: 20px;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
transition: transform 0.3s ease-in-out;
}

.section:hover {
transform: translateY(-5px);
}

.section h2 {
font-size: 1.5rem;
color: #ffffff;
margin-bottom: 10px;
}

.section p {
font-size: 1.1rem;
color: #ffffff;
}

.section a {
display: inline-block;
background-color: #FFB4C2;
color: #ffffff;
text-decoration: none;
padding: 10px 20px;
border-radius: 5px;
margin-top: 10px;
transition: background-color 0.3s ease-in-out;
}

.section a:hover {
background-color: #DA7297;
text-decoration:none;
}

.logout-link {
display: block;
text-align: center;
margin-top: 30px;
background-color: #dc3545;
padding: 5px;
width: 100px;
margin: 0 auto;
border-radius:18px;
height:50px;
display:flex;
justify-content:center;
align-items:center;
}

.logout-link a {
color: white;
text-decoration: none;
padding: 15px;
display: block;
}

.logout-link a:hover {
text-decoration:none;
}
</style>
</head>

<body>
<?php include('../utils/navbar.php'); ?>

<div class="containerr">
<h1>Welcome to Your Organization Dashboard, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
<div class="dashboard-sections">
<div class="section">
<h2>Create New Project</h2>
<p><a href="create_project.php">Create New Project</a></p>
</div>
<div class="section">
<h2>My Projects</h2>
<p><a href="my_projects.php">My Projects</a></p>
</div>
<div class="section">
<h2>Create Volunteer Opportunity</h2>
<p><a href="../volunteer/create_volunteer_opportunity.php">Create Volunteer Opportunity</a></p>
</div>
<div class="section">
<h2>Total Volunteers</h2>
<p><a href="../volunteer/total_volunteers.php">View Total Volunteers</a></p>
</div>

<div class="section">
<h2>Manage Volunteer Opportunities</h2>
<p><a href="../volunteer/manage_volunteers.php">Manage Volunteer Opportunities</a></p>
</div>

<div class="section">
<h2>News Feed</h2>
<p><a href="../news/news_feed.php">View News Feed</a></p>
</div>
<div class="section">
<h2>Create News</h2>
<p><a href="../news/create_news.php">Create News</a></p>
</div>
<div class="section">
<h2 >My News</h2>
<p><a href="../news/my_news.php">Manage My News</a></p>
</div>
 
</div>
</div>
</body>

</html>
