<?php
 session_start();

 if (!isset($_SESSION["user_id"])) {
header("location: ../login.php");
exit;
}

 require_once('../utils/config.php');

 $sql = "SELECT news_id, news_title, cover_image FROM news WHERE is_disapproved = 0"; // Fetch only where is_disapproved is 0 (not disapproved)
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>News Feed</title>
<link rel="stylesheet" href="../styles.css">
<style>
body {
font-family: Arial, sans-serif;
background-color: #FFFAB7;
color: #333;
line-height: 1.6;
margin: 0;
padding: 0;
}

.containerr {
max-width: 1500px;
margin: 20px auto;
padding: 20px;
background-color: #FFFFFF;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
color: #667BC6;
text-align: center;
margin-bottom: 20px;
}

.news-grid {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
gap: 30px;
margin-top: 20px;
}

.news-card {
border: 1px solid #ccc;
border-radius: 8px;
box-shadow: 0 2px 5px rgba(0,0,0,0.1);
overflow: hidden;
transition: transform 0.2s;
}

.news-card:hover {
transform: translateY(-5px);
}

.news-image {
width: 100%;
height: 230px;
object-fit: cover;
}

.news-content {
padding: 15px;
}

.news-title {
font-size: 18px;
margin-bottom: 10px;
color: #333;
}

.news-link {
display: inline-block;
text-decoration: none;
background-color: #DA7297;
color: #ffffff;
padding: 10px 20px;
border-radius: 5px;
margin-top: 10px;
transition: background-color 0.3s ease-in-out;
}

.news-link:hover {
background-color: white;
border:1px solid  #DA7297;
text-decoration:none;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="containerr">
<h1>News Feed</h1>
<div class="news-grid">
<?php
if ($result->num_rows > 0) {
while ($row = $result->fetch_assoc()) {
echo '<div class="news-card">';
echo '<img src="data:image/jpeg;base64,' . base64_encode($row['cover_image']) . '" alt="Cover Image" class="news-image">';
echo '<div class="news-content">';
echo '<h2 class="news-title">' . htmlspecialchars($row['news_title']) . '</h2>';
echo '<a href="view_news.php?news_id=' . $row['news_id'] . '" class="news-link">View News</a>';
echo '</div>';
echo '</div>';
}
} else {
echo '<p>No news available.</p>';
}
// Close connection
$mysqli->close();
?>
</div>
</div>
</body>
</html>
