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

// Fetch all volunteer opportunities managed by the organization
$user_id = $_SESSION['user_id'];

$sql_opportunities = "SELECT vo.volunteer_id, vo.opportunity_name, vo.opportunity_description
FROM volunteer_opportunities vo
WHERE vo.owner_id = ?";
$stmt_opportunities = $mysqli->prepare($sql_opportunities);
$stmt_opportunities->bind_param("i", $user_id);
$stmt_opportunities->execute();
$result_opportunities = $stmt_opportunities->get_result();

$volunteer_opportunities = [];
while ($row = $result_opportunities->fetch_assoc()) {
$volunteer_opportunities[] = $row;
}

$stmt_opportunities->close();

// Fetch all volunteers who have applied for any volunteer opportunity managed by the organization
$sql_volunteers = "SELECT vo.opportunity_name, va.volunteer_card_number, va.name AS applicant_name
FROM volunteer_applications va
JOIN volunteer_opportunities vo ON va.volunteer_id = vo.volunteer_id
WHERE vo.owner_id = ?";
$stmt_volunteers = $mysqli->prepare($sql_volunteers);
$stmt_volunteers->bind_param("i", $user_id);
$stmt_volunteers->execute();
$result_volunteers = $stmt_volunteers->get_result();

$volunteers = [];
while ($row = $result_volunteers->fetch_assoc()) {
$volunteers[] = [
'opportunity_name' => htmlspecialchars($row['opportunity_name']),
'volunteer_card_number' => htmlspecialchars($row['volunteer_card_number']),
'applicant_name' => htmlspecialchars($row['applicant_name']) ?: 'Not provided'
];
}

$stmt_volunteers->close();

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Volunteer Applicants</title>
<link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file for styling -->
<style>
 .containerr {
max-width: 800px;
margin: 20px auto;
padding: 20px;
border: 1px solid #ccc;
border-radius: 5px;
background-color: #f9f9f9;
}
.volunteer-card {
padding: 20px;
border: 1px solid #007BFF;
border-radius: 5px;
margin-bottom: 20px;
background-color: #ffffff;
}
.volunteer-card h3 {
margin: 0 0 10px;
font-size: 20px;
color: #333;
}
.volunteer-card p {
margin: 0;
font-size: 16px;
color: #666;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="containerr">
<h1>All Volunteer Applicants</h1>
<br>
<?php if (!empty($volunteers)): ?>
<?php foreach ($volunteers as $volunteer): ?>
<div class="volunteer-card">
<h3><?php echo htmlspecialchars($volunteer['applicant_name']); ?></h3>
<p>Opportunity Name: <?php echo htmlspecialchars($volunteer['opportunity_name']); ?></p>
<p>Volunteer Card Number: <?php echo htmlspecialchars($volunteer['volunteer_card_number']); ?></p>
</div>
<?php endforeach; ?>
<?php else: ?>
<p>No volunteers found.</p>
<?php endif; ?>

<p><a href="../organization/organization_dashboard.php">Back to Dashboard</a></p>
</div>
</body>
</html>
