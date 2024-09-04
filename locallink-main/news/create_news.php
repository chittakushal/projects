<?php
// Initialize session
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["user_id"])) {
header("location: ../utils/login.php");
exit;
}

// Include the database connection file
require_once('../utils/config.php');

// Define variables and initialize with empty values
$news_title = $news_description = $category = "";
$news_title_err = $news_description_err = $category_err = $cover_image_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Validate news title
if (empty(trim($_POST["news_title"]))) {
$news_title_err = "Please enter a news title.";
} else {
$news_title = trim($_POST["news_title"]);
}

// Validate news description
if (empty(trim($_POST["news_description"]))) {
$news_description_err = "Please enter a news description.";
} else {
$news_description = trim($_POST["news_description"]);
}

// Validate category
if (empty(trim($_POST["category"]))) {
$category_err = "Please enter a category.";
} else {
$category = trim($_POST["category"]);
}

// Validate cover image
if ($_FILES["cover_image"]["error"] == 4) {
$cover_image_err = "Please upload a cover image.";
} else {
$cover_image = file_get_contents($_FILES["cover_image"]["tmp_name"]);
}

// Check input errors before inserting in database
if (empty($news_title_err) && empty($news_description_err) && empty($category_err) && empty($cover_image_err)) {
// Prepare an insert statement
$sql = "INSERT INTO news (news_title, news_description, cover_image, category, owner_id) VALUES (?, ?, ?, ?, ?)";

if ($stmt = $mysqli->prepare($sql)) {
// Bind variables to the prepared statement as parameters
$stmt->bind_param("ssssi", $param_title, $param_description, $param_image, $param_category, $param_owner_id);

// Set parameters
$param_title = $news_title;
$param_description = $news_description;
$param_image = $cover_image;
$param_category = $category;
$param_owner_id = $_SESSION["user_id"];

// Attempt to execute the prepared statement
if ($stmt->execute()) {
// Redirect to my news page
header("location: my_news.php");
exit();
} else {
echo "Something went wrong. Please try again later.";
}

// Close statement
$stmt->close();
}
}

// Close connection
$mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create News</title>
<link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file for styling -->
<style>
.wrapper {
width: 500px;
margin: 0 auto;
}
.form-group {
margin-bottom: 15px;
}
.form-group label {
display: block;
margin-bottom: 5px;
}
.form-group input, .form-group textarea {
width: 100%;
padding: 8px;
box-sizing: border-box;
}
.form-group .help-block {
color: red;
}
.btn-primary {
padding: 10px 15px;
background-color: #007BFF;
color: white;
border: none;
cursor: pointer;
}
.btn-primary:hover {
background-color: #0056b3;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>
<br><br><br>
<div class="wrapper">
<h2>Create News</h2>
<p>Please fill this form to create a news article.</p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
<div class="form-group <?php echo (!empty($news_title_err)) ? 'has-error' : ''; ?>">
<label>News Title</label>
<input type="text" name="news_title" class="form-control" value="<?php echo $news_title; ?>">
<span class="help-block"><?php echo $news_title_err; ?></span>
</div>
<div class="form-group <?php echo (!empty($news_description_err)) ? 'has-error' : ''; ?>">
<label>News Description</label>
<textarea name="news_description" class="form-control" rows="10" cols="10"><?php echo $news_description; ?></textarea>
<span class="help-block"><?php echo $news_description_err; ?></span>
</div>
<div class="form-group <?php echo (!empty($category_err)) ? 'has-error' : ''; ?>">
<label>Category</label>
<input type="text" name="category" class="form-control" value="<?php echo $category; ?>">
<span class="help-block"><?php echo $category_err; ?></span>
</div>
<div class="form-group <?php echo (!empty($cover_image_err)) ? 'has-error' : ''; ?>">
<label>Cover Image</label>
<input type="file" name="cover_image" class="form-control">
<span class="help-block"><?php echo $cover_image_err; ?></span>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary" value="Submit">
</div>
</form>
</div>    
</body>
</html>
