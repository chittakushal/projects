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

// Initialize variables
$opportunity_name = $opportunity_description = "";
$owner_id = $_SESSION["user_id"];
$cover_image = null;
$message = ""; // Variable to store success or error message

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$opportunity_name = $_POST['opportunity_name'];
$opportunity_description = $_POST['opportunity_description'];

// Handle file upload for cover image
if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == UPLOAD_ERR_OK) {
$cover_image = file_get_contents($_FILES['cover_image']['tmp_name']);
}

// Insert into volunteer_opportunities table
$sql = "INSERT INTO volunteer_opportunities (opportunity_name, opportunity_description, cover_image, owner_id) VALUES (?, ?, ?, ?)";
if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("ssbi", $opportunity_name, $opportunity_description, $cover_image, $owner_id);
$stmt->send_long_data(2, $cover_image);

if ($stmt->execute()) {
$message = "Volunteer opportunity created successfully!";
} else {
$message = "Error: " . $mysqli->error;
}

$stmt->close();
} else {
$message = "Error preparing statement: " . $mysqli->error;
}
}

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Volunteer Opportunity</title>
<style>
body {
font-family: Arial, sans-serif;
background-color: #f4f4f4;
margin: 0;
padding: 0;
}
.containerr {
width: 80%;
margin: 20px auto;
padding: 20px;
background-color: #fff;
box-shadow: 0 2px 5px rgba(0,0,0,0.15);
border-radius: 5px;
}
h1 {
font-size: 24px;
color: #333;
}
.form-group {
margin-bottom: 15px;
}
.form-group label {
display: block;
font-weight: bold;
margin-bottom: 5px;
}
.form-group input, .form-group textarea {
width: 100%;
padding: 8px;
border: 1px solid #ccc;
border-radius: 5px;
}
.form-group input[type="file"] {
padding: 3px;
}
.form-group button {
padding: 10px 15px;
background-color: #007BFF;
color: #fff;
border: none;
cursor: pointer;
border-radius: 5px;
transition: background-color 0.3s;
}
.form-group button:hover {
background-color: #0056b3;
}
.message {
margin-top: 10px;
padding: 10px;
background-color: #d4edda;
color: #155724;
border: 1px solid #c3e6cb;
border-radius: 5px;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="containerr">
<h1>Create Volunteer Opportunity</h1>
<?php if (!empty($message)) : ?>
<div class="message"><?php echo $message; ?></div>
<?php endif; ?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
<div class="form-group">
<label for="opportunity_name">Opportunity Name:</label>
<input type="text" id="opportunity_name" name="opportunity_name" required>
</div>
<div class="form-group">
<label for="opportunity_description">Opportunity Description:</label>
<textarea id="opportunity_description" name="opportunity_description" rows="10" cols="20" required></textarea>
</div>
<div class="form-group">
<label for="cover_image">Cover Image:</label>
<input type="file" id="cover_image" name="cover_image" accept="image/*">
</div>
<div class="form-group">
<button type="submit">Create Opportunity</button>
</div>
</form>
</div>
</body>
</html>
