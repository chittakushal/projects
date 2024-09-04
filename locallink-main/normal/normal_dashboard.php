<?php
 session_start();

 if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'normal') {
header("location: ../utils/login.php");
exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Normal User Dashboard</title>
<link rel="stylesheet" href="../styles.css">
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
<h1>Welcome to Your Dashboard, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
<div class="dashboard-sections">
<div class="section">
<h2>News Feed</h2>
<p><a href="../news/news_feed.php">View News Feed</a></p>
</div>
<div class="section">
<h2>My News</h2>
<p><a href="../news/my_news.php">Manage My News</a></p>
</div>
<div class="section">
<h2>Events</h2>
<p><a href="../events/events_feed.php">View Events</a></p>
</div>
<div class="section">
<h2>My Registered Events</h2>
<p><a href="../events/register_event.php">View My Registered Events</a></p>
</div>
<div class="section">
<h2>Projects</h2>
<p><a href="../organization/projects_feed.php">View Projects</a></p>
</div>
<div class="section">
<h2>Volunteer Opportunities</h2>
<p><a href="../volunteer/volunteer_opportunities_feed.php">View My Volunteer Opportunities</a></p>
</div>
<div class="section">
<h2>Discussions</h2>
<p><a href="../discussions/discussions.php">Join Discussions</a></p>
</div>
<br>
</div>
 
</div>
</body>
</html>
