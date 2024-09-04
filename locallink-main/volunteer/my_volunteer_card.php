<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'normal') {
header("location: ../utils/login.php");
exit;
}

require_once('../utils/config.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT va.application_id, va.volunteer_card_number, vo.opportunity_name
FROM volunteer_applications va
INNER JOIN volunteer_opportunities vo ON va.volunteer_id = vo.volunteer_id
WHERE va.user_id = $user_id";

$result = $mysqli->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Volunteer Card</title>
<style>
.wrapper {
max-width: 600px;
margin: 0 auto;
padding: 20px;
}
.volunteer-card {
margin-bottom: 20px;
padding: 10px;
border: 1px solid #ccc;
border-radius: 5px;
background-color: #f9f9f9;
}
.volunteer-card h3 {
margin-top: 10px;
font-size: 20px;
color: #333;
}
.volunteer-card p {
color: #666;
margin-bottom: 10px;
}
.btn {
display: inline-block;
padding: 8px 16px;
background-color: #DA7297;
color: #fff;
text-decoration: none;
border-radius: 4px;
transition: background-color 0.3s;
}
.btn:hover {
background-color: #DA7297;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="wrapper">
<h2>My Volunteer Card</h2>

<?php if ($result && $result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<div class="volunteer-card">
<h3><?php echo htmlspecialchars($row['opportunity_name']); ?></h3>
<p><strong>Volunteer Card Number:</strong> <?php echo htmlspecialchars($row['volunteer_card_number']); ?></p>
<a href="volunteer_opportunities_feed.php" class="btn">Back to Volunteer Feed</a>
</div>
<?php endwhile; ?>
<?php else: ?>
<p>No volunteer applications found.</p>
<a href="volunteer_opportunities_feed.php" class="btn">Back to Volunteer Feed</a>
<?php endif; ?>
</div>
</body>
</html>

<?php
 $mysqli->close();
?>
