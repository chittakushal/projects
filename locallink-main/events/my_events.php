<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Events</title>
<link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file for styling -->
<style>
body {
font-family: Arial, sans-serif;
background-color: #f4f4f4;
margin: 0;
padding: 0;
}
h1 {
text-align: center;
margin: 20px 0;
}
.containerr {
width: 90%;
margin: 20px auto;
display: flex;
flex-wrap: wrap;
gap: 20px;
}
.card {
width: 300px;  
height: 400px; 
background-color: #ffffff;
box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
border-radius: 5px;
overflow: hidden;
margin-bottom: 20px;
}
.card img {
width: 100%;
height: 200px;  
object-fit: cover;
border-radius: 5px 5px 0 0;
}
.card .content {
padding: 15px;
height: calc(100% - 215px);  
box-sizing: border-box;  
overflow: hidden;  
}
.card .content h3 {
margin: 0 0 10px;
font-size: 1.2rem;
}
.card .content p {
margin: 0;
font-size: 1rem;
color: #666666;
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
}
 
.btn-danger {
background-color: #DA7297;
}
.btn-danger:hover {
background-color: #b85979;
}
.message {
width: 90%;
margin: 20px auto;
padding: 20px;
background-color: #FFB4C2;
color: #ffffff;
border: 1px solid #ff97a1;
border-radius: 5px;
text-align: center;
}
.cont{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:30px;
}
.cont span{
    background-color:#FFB4C2;
    padding:10px;
    color:white;
    border-radius:18px;
}
span  a{
    color:white;
}
span a:hover{
    text-decoration:none;
}

.navbar {
border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Bottom border */
padding-top: 0.5rem;
padding-bottom: 0.5rem;
}

.navbar-brand {
font-size: 1.8rem;  
font-weight: bold;
color: #333; 
}

.navbar-toggler {
border: none; 
}

.navbar-nav .nav-item {
margin-left: 15px;  
}

.navbar-nav .nav-link {
color: #333;  
font-weight: 500;
transition: color 0.3s ease;
}

.navbar-nav .nav-link:hover {
color: #007bff;  
}

.navbar-collapse {
justify-content: space-between;  
}
</style>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<body>
 

<nav class="navbar navbar-expand-lg navbar-light bg-light">
<div class="container">
<a class="navbar-brand" href="/local/index.php">LocalLink</a>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav ml-auto">
<li class="nav-item">
<a class="nav-link" href="../business/business_dashboard.php">Dashboard</a>
</li>
<li class="nav-item">
<a class="nav-link" href="../utils/logout.php">Logout</a>
</li>
</ul>
</div>
</div>
</nav>

 
<div class="containerr">
<?php
 session_start();

 if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'business') {
header("location: ../utils/login.php");
exit;
}

 require_once('../utils/config.php');

 $user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM events WHERE owner_id = ?";
$events = [];

if ($stmt = $mysqli->prepare($sql)) {
 $stmt->bind_param("i", $user_id);

 if ($stmt->execute()) {
 $result = $stmt->get_result();

 if ($result->num_rows > 0) {
 $events = $result->fetch_all(MYSQLI_ASSOC);
} else {
echo '<div class="message"><p>No events found.</p></div>';
}
} else {
echo '<div class="message"><p>Error executing query: ' . $mysqli->error . '</p></div>';
}

 $stmt->close();
}

 $mysqli->close();
?>

<?php foreach ($events as $event): ?>
<div class="card">
<img src="data:image/jpeg;base64,<?php echo base64_encode($event['cover_image']); ?>" alt="Cover Image">
<div class="buttons">
<a href="update_event.php?event_id=<?php echo $event['event_id']; ?>" class="btns">Update</a>
<a href="delete_event.php?event_id=<?php echo $event['event_id']; ?>" class="btn btn-danger">Delete</a>
</div>
<div class="content">
<h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
<p><?php echo htmlspecialchars($event['event_description']); ?></p>
</div>
</div>
<?php endforeach; ?>

<?php if (empty($events)): ?>
<div class="message"><p>No events found.</p></div>
<?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
