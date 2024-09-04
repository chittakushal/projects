<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'normal') {
header("location: ../utils/login.php");
exit;
}

require_once('../utils/config.php');

$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
echo "Event ID not specified.";
exit();
}

$sql = "SELECT event_name, event_description, cover_image, start_date, end_date, venue,event_contact_email 
FROM events 
WHERE event_id = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt) {
$stmt->bind_param("i", $event_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
$stmt->bind_result($event_name, $event_description, $cover_image, $start_date, $end_date, $venue,$event_contact_email);
$stmt->fetch();

function convertToHtml($text) {
$patterns = [
'/h1:(.*?):h1/' => '<h1>$1</h1>',
'/h2:(.*?):h2/' => '<h2>$1</h2>',
'/h3:(.*?):h3/' => '<h3>$1</h3>',
'/h4:(.*?):h4/' => '<h4>$1</h4>',
'/h5:(.*?):h5/' => '<h5>$1</h5>',
'/h6:(.*?):h6/' => '<h6>$1</h6>',
];
foreach ($patterns as $pattern => $replacement) {
$text = preg_replace($pattern, $replacement, $text);
}
return nl2br($text);
}

$event_description = convertToHtml($event_description);
$paragraphs = preg_split('/(?<=[.?!])\s+(?=[a-zA-Z])/', $event_description);
} else {
echo "Event not found.";
exit();
}

$stmt->close();
} else {
echo "Failed to prepare statement: " . $mysqli->error;
exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Details</title>
<link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file for styling -->
<style>
.wrapper {
width: 90%;
margin: 0 auto;
}

.event-details {
margin-top: 20px;
}

.event-card {
border: 1px solid #ccc;
border-radius: 5px;
padding: 10px;
margin-bottom: 20px;
overflow: hidden;
}

.event-card img {
width: 300px;
height: auto;
margin-right: 15px;
border-radius: 5px;
}

.event-card h3 {
font-size: 20px;
margin-top: 0;
color: #333;
}

.event-card p {
margin-bottom: 10px;
color: #666;
}

.event-card .btn {
display: inline-block;
padding: 8px 16px;
background-color: #667BC6;
color: #fff;
text-decoration: none;
border-radius: 4px;
transition: background-color 0.3s;
margin-right: 10px;
}

.event-card .btn:hover {
background-color: #4A5A9F;
}

h2 {
margin-left: 10px;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="wrapper">
<h2>Event Details</h2>
<div class="event-details">
<div class="event-card">
<img src="data:image/jpeg;base64,<?php echo base64_encode($cover_image); ?>" alt="Cover Image">
<h3><?php echo htmlspecialchars($event_name); ?></h3>
<?php foreach ($paragraphs as $paragraph): ?>
<?php echo $paragraph; ?>
<?php endforeach; ?>
<p><strong>Venue:</strong> <?php echo htmlspecialchars($venue); ?></p>
<p><strong>Contact E-Mail:</strong> <?php echo htmlspecialchars($event_contact_email); ?></p>

<p><strong>Start Date:</strong> <?php echo htmlspecialchars($start_date); ?></p>
<p><strong>End Date:</strong> <?php echo htmlspecialchars($end_date); ?></p>
<a href="event_register.php?event_id=<?php echo $event_id; ?>" class="btn">Register for this Event</a><br><br><br>
<a href="events_feed.php" class="btn">Back to Events</a>
</div>
</div>
</div>
</body>
</html>

<?php
$mysqli->close();
?>
