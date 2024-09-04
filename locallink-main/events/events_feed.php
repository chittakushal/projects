<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'normal') {
header("location: ../utils/login.php");
exit;
}

require_once('../utils/config.php');

function convertCustomTagsToHTML($text) {
$patterns = [
'/h1:(.*?):h1/' => '<h1>$1</h1>',
'/h2:(.*?):h2/' => '<h2>$1</h2>',
'/h3:(.*?):h3/' => '<h3>$1</h3>',
'/h4:(.*?):h4/' => '<h4>$1</h4>',
'/h5:(.*?):h5/' => '<h5>$1</h5>',
'/h6:(.*?):h6/' => '<h6>$1</h6>',
];

$html = preg_replace(array_keys($patterns), array_values($patterns), $text);
$html = preg_replace('/(?!<\/?(h1|h2|h3|h4|h5|h6)>)[^\n]+/', '<p>$0</p>', $html);

return $html;
}

$sql = "SELECT event_id, event_name, event_description, cover_image, start_date, end_date, venue 
FROM events 
WHERE is_disapproved = 0";

$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Events Feed</title>
<style>
body {
font-family: Arial, sans-serif;
background-color: #f0f0f0;
color: #333;
margin: 0;
padding: 0;
}

.wrapper {
max-width: 800px;
margin: 20px auto;
padding: 20px;
background-color: #fff;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
color: #DA7297;
}

.event-card {
margin-bottom: 20px;
padding: 10px;
border: 1px solid #ccc;
border-radius: 5px;
}

.event-card img {
width: 100%;
max-width: 300px;
height: auto;
border-radius: 5px;
}

.event-card h1, .event-card h2, .event-card h3, .event-card h4, .event-card h5, .event-card h6 {
margin-top: 10px;
font-size: 20px;
color: #333; /* Dark text color */
}

.event-card p {
color: #666; /* Gray text */
text-align: justify;
}

.event-card .btn {
display: inline-block;
margin-top: 10px;
padding: 8px 16px;
background-color: #667BC6;
color: #fff;
text-decoration: none;
border-radius: 4px;
transition: background-color 0.3s;
}

.event-card .btn:hover {
background-color: #4A5A9F;
color: #fff;
}
</style>
<link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file for additional styling -->
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="wrapper">
<h2>Events Feed</h2>
<?php if ($result->num_rows > 0): ?>
<div class="events">
<?php while ($row = $result->fetch_assoc()): ?>
<div class="event-card">
<img src="data:image/jpeg;base64,<?php echo base64_encode($row['cover_image']); ?>" alt="Cover Image">
<?php echo convertCustomTagsToHTML($row['event_name']); ?>
<p>
<?php
$description = convertCustomTagsToHTML(htmlspecialchars($row['event_description']));
$words = explode(' ', strip_tags($description));
$truncatedDescription = implode(' ', array_slice($words, 0, 20));
echo $truncatedDescription . '...';
?>
</p>
<p><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
<p><strong>Start Date:</strong> <?php echo htmlspecialchars($row['start_date']); ?></p>
<p><strong>End Date:</strong> <?php echo htmlspecialchars($row['end_date']); ?></p>
<a href="event_register.php?event_id=<?php echo $row['event_id']; ?>" class="btn">Register for this Event</a>
<a href="event_details.php?event_id=<?php echo $row['event_id']; ?>" class="btn">View Details</a>
</div>
<?php endwhile; ?>
</div>
<?php else: ?>
<p>No events found.</p>
<?php endif; ?>
</div>
</body>
</html>

<?php
$mysqli->close();
?>
