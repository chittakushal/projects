<?php
session_start();

if (!isset($_SESSION["user_id"])) {
header("location: ../login.php");
exit;
}

require_once('../utils/config.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT news_id, news_title, cover_image FROM news WHERE owner_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My News</title>
<link rel="stylesheet" href="../styles.css">
<style>
.containerr {
max-width: 1200px;
margin: 20px auto;
padding: 20px;
background-color: #ffffff;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
font-size: 2.5rem;
color: #DA7297;
text-align: center;
margin-bottom: 20px;
}

.create-news-btn {
display: block;
margin: 20px auto;
padding: 10px 20px;
background-color:#FFB4C2;
color: #ffffff;
text-decoration: none;
text-align: center;
border-radius: 5px;
font-weight: bold;
transition: background-color 0.3s ease-in-out;
}

.create-news-btn:hover {
background-color: #218838;
text-decoration:none;
background-color:#ffffff;
color:#FFB4C2 ;
border:1px solid #FFB4C2;
}

.news-container {
display: flex;
flex-wrap: wrap;
gap: 20px;
justify-content: center;
margin-top: 20px;
}

.news-card {
border: 1px solid #ccc;
border-radius: 8px;
width: 300px;
box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
overflow: hidden;
transition: transform 0.2s;
position: relative;
}

.news-card:hover {
transform: scale(1.05);
}

.news-image {
width: 100%;
height: 200px;
object-fit: cover;
}

.news-content {
padding: 15px;
}

.news-title {
font-size: 18px;
margin-bottom: 10px;
color: #333333;
}

.news-link {
text-decoration: none;
color: #007BFF;
font-weight: bold;
transition: color 0.3s;
}

.news-link:hover {
color: #0056b3;
}

.news-actions {
margin-top: 10px;
}

.news-actions a {
margin-right: 10px;
text-decoration: none;
padding: 8px 15px;
border-radius: 5px;
font-weight: bold;
transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
}

.news-actions a:hover {
background-color: #FFB4C2;
color: #ffffff;
}

.delete-form {
display: inline;
}

.delete-form button {
padding: 8px;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="containerr">
<h1>My News</h1>
<a href="create_news.php" class="create-news-btn">Create News</a>
<div class="news-container">
<?php
if ($result->num_rows > 0) {
while ($row = $result->fetch_assoc()) {
echo '<div class="news-card">';
echo '<img src="data:image/jpeg;base64,' . base64_encode($row['cover_image']) . '" alt="Cover Image" class="news-image">';
echo '<div class="news-content">';
echo '<h2 class="news-title">' . htmlspecialchars($row['news_title']) . '</h2>';
echo '<a href="view_news.php?news_id=' . $row['news_id'] . '" class="news-link">View News</a>';
echo '<div class="news-actions">';
echo '<a href="update_news.php?news_id=' . $row['news_id'] . '" class="btn btn-primary">Update</a>';
echo '<form action="delete_news.php" method="post" class="delete-form">';
echo '<input type="hidden" name="news_id" value="' . $row['news_id'] . '">';
echo '<button type="submit" class="btn btn-danger">Delete</button>';
echo '</form>';
echo '</div>';
echo '</div>';
echo '</div>';
}
} else {
echo '<p>No news articles found.</p>';
}
// Close connection
$mysqli->close();
?>
</div>
</div>
</body>
</html>
