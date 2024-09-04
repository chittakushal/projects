<?php
// Initialize session
session_start();

// Check if the user is logged in and has the role 'normal'
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'normal') {
header("location: ../utils/login.php");
exit;
}

// Include the database connection file
require_once('../utils/config.php');

// Check if the volunteer_id is provided via GET
if (!isset($_GET['volunteer_id']) || !is_numeric($_GET['volunteer_id'])) {
header("location: volunteer_feed.php");
exit;
}

$volunteer_id = $_GET['volunteer_id'];
$user_id = $_SESSION['user_id'];

// Check if the user has already applied for this volunteer opportunity
$sql_check_application = "SELECT * FROM volunteer_applications WHERE volunteer_id = $volunteer_id AND user_id = $user_id";
$result_check_application = $mysqli->query($sql_check_application);

if ($result_check_application && $result_check_application->num_rows > 0) {
// User has already applied, redirect to their volunteer card
header("location: my_volunteer_card.php");
exit;
}

// Fetch the volunteer opportunity details
$sql_volunteer_opportunity = "SELECT * FROM volunteer_opportunities WHERE volunteer_id = $volunteer_id";
$result_volunteer_opportunity = $mysqli->query($sql_volunteer_opportunity);

if (!$result_volunteer_opportunity || $result_volunteer_opportunity->num_rows === 0) {
// Volunteer opportunity not found
header("location: volunteer_feed.php");
exit;
}

$row = $result_volunteer_opportunity->fetch_assoc();
$opportunity_name = htmlspecialchars($row['opportunity_name']);
$opportunity_description = htmlspecialchars($row['opportunity_description']);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Generate a random volunteer card number
$volunteer_card_number = generateRandomString(); // Function to generate random string

// Validate and process the application
$name = $_POST['name'];

// Insert the application into the database
$sql_insert_application = "INSERT INTO volunteer_applications (volunteer_id, user_id, volunteer_card_number, name) 
VALUES ($volunteer_id, $user_id, '$volunteer_card_number', '$name')";

if ($mysqli->query($sql_insert_application) === TRUE) {
// Application successful, redirect to volunteer card
header("location: my_volunteer_card.php");
exit;
} else {
// Error inserting application
$error_message = "Error applying for volunteer opportunity: " . $mysqli->error;
}
}

// Function to generate a random string (volunteer card number)
function generateRandomString($length = 10) {
$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$charactersLength = strlen($characters);
$randomString = '';
for ($i = 0; $i < $length; $i++) {
$randomString .= $characters[rand(0, $charactersLength - 1)];
}
return $randomString;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Apply for Volunteer Opportunity</title>
 <style>
 .wrapper {
max-width: 600px;
margin: 0 auto;
padding: 20px;
}
.form-group {
margin-bottom: 15px;
}
.form-group label {
display: block;
margin-bottom: 5px;
font-weight: bold;
}
.form-group input[type="text"],
.form-group input[type="number"] {
width: 100%;
padding: 8px;
font-size: 14px;
border: 1px solid #ccc;
border-radius: 4px;
box-sizing: border-box;
}
.btns {
display: inline-block;
padding: 8px 16px;
background-color: #DA7297;
color: #fff;
text-decoration: none;
border-radius: 4px;
transition: background-color 0.3s;
border:1px solid white;
}
.btns:hover {
background-color: #3DA7297;
text-decoration:none;
color:white;
}
.error {
color: red;
font-weight: bold;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="wrapper">
<h2>Apply for Volunteer Opportunity: <?php echo $opportunity_name; ?></h2>
<p><?php echo $opportunity_description; ?></p>

<?php if (isset($error_message)): ?>
<p class="error"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?volunteer_id=$volunteer_id"; ?>">
<div class="form-group">
<label for="name">Your Name:</label>
<input type="text" id="name" name="name" required>
</div>
<button type="submit" class="btns">Apply</button>
</form>

<br>
<a href="volunteer_opportunities_feed.php" class="btns">Back to Volunteer Feed</a>
</div>
</body>
</html>

<?php
// Close connection
$mysqli->close();
?>
