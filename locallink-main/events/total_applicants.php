<?php
// Initialize session
session_start();

// Check if the user is logged in and is a business user, if not then redirect to login page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'business') {
header("location: ../login.php");
exit;
}

// Include the database connection file
require_once('../utils/config.php');

// Get user ID from session
$user_id = $_SESSION["user_id"];

// Fetch events created by the business user and their applications count
$sql = "SELECT e.event_id, e.event_name, COUNT(r.registration_id) as applications_count 
FROM events e 
LEFT JOIN event_registrations r ON e.event_id = r.event_id 
WHERE e.owner_id = ?
GROUP BY e.event_id, e.event_name";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
// Fetch the names of the applicants for each event
$sql_applicants = "SELECT u.username 
FROM event_registrations r 
JOIN users u ON r.user_id = u.user_id 
WHERE r.event_id = ?";
$stmt_applicants = $mysqli->prepare($sql_applicants);
$stmt_applicants->bind_param("i", $row['event_id']);
$stmt_applicants->execute();
$result_applicants = $stmt_applicants->get_result();

$applicants = [];
while ($applicant_row = $result_applicants->fetch_assoc()) {
$applicants[] = $applicant_row['username'];
}

$row['applicants'] = $applicants;
$events[] = $row;

$stmt_applicants->close();
}

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Total Applicants</title>
<style>
/* Global Styles */
body {
font-family: Arial, sans-serif;
line-height: 1.6;
background-color: #f4f4f4;
margin: 0;
padding: 0;
}

.wrapper {
max-width: 800px;
margin: 20px auto;
background: #fff;
padding: 20px;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

table {
width: 100%;
border-collapse: collapse;
}

table, th, td {
border: 1px solid #ccc;
}

th, td {
padding: 8px;
text-align: left;
}

th {
background-color: #f4f4f4;
}

.btn {
display: inline-block;
padding: 8px 16px;
background-color: #007bff;
color: #fff;
text-decoration: none;
border-radius: 4px;
margin-top: 10px;
transition: background-color 0.3s ease;
}

.btn:hover {
background-color: #0056b3;
}
.btns{
    margin-top:15px;
    background-color:#DA7297;
    color:white;
    padding:10px;
}
 
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>


 

<div class="wrapper">
<h2>Total Applicants for Your Events</h2>
<?php if (!empty($events)): ?>
<table>
<thead>
<tr>
<th>Event Name</th>
<th>Total Applicants</th>
<th>Applicants</th>
</tr>
</thead>
<tbody>
<?php foreach ($events as $event): ?>
<tr>
<td><?php echo htmlspecialchars($event['event_name']); ?></td>
<td><?php echo htmlspecialchars($event['applications_count']); ?></td>
<td>
<?php if (!empty($event['applicants'])): ?>
<ul>
<?php foreach ($event['applicants'] as $applicant): ?>
<li><?php echo htmlspecialchars($applicant); ?></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p>No applicants found.</p>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p>No events found.</p>
<?php endif; ?>
<br><br><br>
<p><a href="../business/business_dashboard.php" class="btns">Back to Dashboard</a></p>
</div>
</body>
</html>
