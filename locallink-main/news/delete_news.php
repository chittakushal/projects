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

// Check if news_id is set in the URL
if (isset($_POST['news_id'])) {
$news_id = $_POST['news_id'];

// Prepare a delete statement
$sql = "DELETE FROM news WHERE news_id = ? AND owner_id = ?";

if ($stmt = $mysqli->prepare($sql)) {
// Bind variables to the prepared statement as parameters
$stmt->bind_param("ii", $param_news_id, $param_owner_id);

// Set parameters
$param_news_id = $news_id;
$param_owner_id = $_SESSION["user_id"];

// Attempt to execute the prepared statement
if ($stmt->execute()) {
// Redirect to my news page after successful deletion
header("location: my_news.php");
exit();
} else {
echo "Oops! Something went wrong. Please try again later.";
}

// Close statement
$stmt->close();
}
} else {
// If news_id is not set, redirect to my news page
header("location: my_news.php");
exit();
}

// Close connection
$mysqli->close();
?>
