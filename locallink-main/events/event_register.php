<?php
// Initialize session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'normal') {
header("location: ../utils/login.php");
exit;
}

// Include the database connection file
require_once('../utils/config.php');

// Initialize variables
$user_id = $_SESSION["user_id"];
$event_id = $_GET["event_id"] ?? null;

// Fetch event details from database
$sql_event = "SELECT event_id, event_name, start_date, end_date FROM events WHERE event_id = ?";
$stmt_event = $mysqli->prepare($sql_event);
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$result_event = $stmt_event->get_result();

if ($result_event->num_rows == 1) {
$event = $result_event->fetch_assoc();
$event_name = $event['event_name'];
$start_date = $event['start_date'];
$end_date = $event['end_date'];
} else {
echo "Event not found.";
exit();
}

$stmt_event->close();

// Check if user is already registered for the event
$sql_check = "SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?";
$stmt_check = $mysqli->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $event_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
echo '<div class="message">';
echo "<p>You are already registered for this event.</p>";
echo '<a href="events_feed.php" class="btn btn-primary">Go to Event Feed</a>';
echo '</div>';
exit();
}

$stmt_check->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Insert registration into database
$sql_insert = "INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)";
$stmt_insert = $mysqli->prepare($sql_insert);
$stmt_insert->bind_param("ii", $user_id, $event_id);

if ($stmt_insert->execute()) {
// Increment applications_count in events table
$sql_update = "UPDATE events SET applications_count = applications_count + 1 WHERE event_id = ?";
$stmt_update = $mysqli->prepare($sql_update);
$stmt_update->bind_param("i", $event_id);
$stmt_update->execute();

// Redirect to event details page or any confirmation page
header("location: register_event.php");
exit();
} else {
echo "Failed to register for the event. Please try again.";
}

$stmt_insert->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Registration</title>
<style>
body {
font-family: Arial, sans-serif;
background-color: #f4f4f4;
margin: 0;
padding: 0;
}
.wrapper {
width: 500px;
margin: 50px auto;
padding: 20px;
background-color: #fff;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
border-radius: 5px;
}
h2 {
margin-top: 0;
}
.btn {
display: inline-block;
padding: 10px 20px;
text-decoration: none;
border-radius: 5px;
margin: 5px 0;
}
.btn-primary {
background-color: #007bff;
color: #fff;
}
.btn-primary:hover {
background-color: #0056b3;
}
.btn-secondary {
background-color: #6c757d;
color: #fff;
}
.btn-secondary:hover {
background-color: #5a6268;
}
.message {
width: 500px;
margin: 50px auto;
padding: 20px;
background-color: #d4edda;
color: #155724;
border: 1px solid #c3e6cb;
border-radius: 5px;
text-align: center;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="wrapper">
<h2>Event Registration: <?php echo htmlspecialchars($event_name); ?></h2>
<p><strong>Event Date:</strong> <?php echo htmlspecialchars($start_date); ?> to <?php echo htmlspecialchars($end_date); ?></p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?event_id=$event_id"; ?>" method="post">
<button type="submit" class="btn btn-primary">Register for Event</button>
<a href="event_details.php?event_id=<?php echo $event_id; ?>" class="btn btn-secondary">Cancel</a>
</form>
</div>
</body>
</html>
