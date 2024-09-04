<?php
 session_start();

 if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'organization') {
header("location: ../utils/login.php");
exit;
}

 require_once('../utils/config.php');

 if (isset($_GET['volunteer_id'])) {
$volunteer_id = $_GET['volunteer_id'];

 $sql = "SELECT * FROM volunteer_opportunities WHERE volunteer_id = ? AND owner_id = ?";
if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("ii", $volunteer_id, $_SESSION["user_id"]);
if ($stmt->execute()) {
$result = $stmt->get_result();
if ($result->num_rows == 1) {
$volunteer_opportunity = $result->fetch_assoc();
} else {
echo "Volunteer opportunity not found or you don't have permission to edit this.";
exit;
}
} else {
echo "Error executing query: " . $mysqli->error;
exit;
}
$stmt->close();
} else {
echo "Error preparing statement: " . $mysqli->error;
exit;
}
} else {
echo "Volunteer opportunity ID not provided.";
exit;
}

 if ($_SERVER["REQUEST_METHOD"] == "POST") {
$opportunity_name = $_POST["opportunity_name"];
$opportunity_description = $_POST["opportunity_description"];
$cover_image = $_FILES["cover_image"]["tmp_name"] ? file_get_contents($_FILES["cover_image"]["tmp_name"]) : null;

$sql = "UPDATE volunteer_opportunities SET opportunity_name = ?, opportunity_description = ?, cover_image = ? WHERE volunteer_id = ? AND owner_id = ?";
if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("sssii", $opportunity_name, $opportunity_description, $cover_image, $volunteer_id, $_SESSION["user_id"]);
if ($stmt->execute()) {
$success_message = "Volunteer opportunity updated successfully.";
} else {
echo "Error executing query: " . $mysqli->error;
}
$stmt->close();
} else {
echo "Error preparing statement: " . $mysqli->error;
}
}

 $mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
font-family: Arial, sans-serif;
background-color: #f0f0f0;
margin: 0;
padding: 0;
}

.containerr {
max-width: 90%;
margin: 20px auto;
background-color: #ffffff;
padding: 20px;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
color: #667BC6;
text-align:center;
}

form {
margin-top: 20px;
}

label {
font-weight: bold;
}

input[type="text"],
textarea {
width: calc(100% - 20px);
padding: 8px;
margin-bottom: 10px;
border: 1px solid #ccc;
border-radius: 4px;
box-sizing: border-box;
}

textarea {
resize: vertical;
}

input[type="file"] {
margin-top: 5px;
}

input[type="submit"] {
background-color: #667BC6;
color: white;
padding: 10px 20px;
border: none;
border-radius: 4px;
cursor: pointer;
}

input[type="submit"]:hover {
background-color: #5067A8;
}

.success-message {
background-color: #d4edda;
border-color: #c3e6cb;
color: #155724;
padding: 10px;
margin-top: 10px;
border-radius: 4px;
display: <?php echo isset($success_message) ? 'block' : 'none'; ?>;
}
</style>
<title>Update Volunteer Opportunity</title>
</head>
<body>
<?php include('../utils/navbar.php'); ?>
<br>
<h1>Update Volunteer Opportunity</h1>
<div class="containerr">
<?php if (isset($success_message)) : ?>
<div class="success-message"><?php echo $success_message; ?></div>
<?php endif; ?>

<form action="update_volunteer_opportunity.php?volunteer_id=<?php echo $volunteer_id; ?>" method="post" enctype="multipart/form-data">
<div>
<label for="opportunity_name">Opportunity Name:</label>
<input type="text" id="opportunity_name" name="opportunity_name" value="<?php echo htmlspecialchars($volunteer_opportunity['opportunity_name']); ?>" required>
</div>
<div>
<label for="opportunity_description">Opportunity Description:</label>
<textarea id="opportunity_description" name="opportunity_description" rows="10" cols="20" required><?php echo htmlspecialchars($volunteer_opportunity['opportunity_description']); ?></textarea>
</div>
<div>
<label for="cover_image">Cover Image:</label>
<input type="file" id="cover_image" name="cover_image">
</div>
<div>
<input type="submit" value="Update Volunteer Opportunity">
</div>
</form>
</div>

</body>
</html>
