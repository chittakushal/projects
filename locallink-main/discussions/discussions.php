<?php
 session_start();

 if (!isset($_SESSION["user_id"])) {
header("location: ../login.php");
exit;
}

 require_once('../utils/config.php');

 $discussion_topic = $message = "";
$discussion_topic_err = $message_err = "";

 if ($_SERVER["REQUEST_METHOD"] == "POST") {
 if (empty(trim($_POST["discussion_topic"]))) {
$discussion_topic_err = "Please enter a discussion topic.";
} else {
$discussion_topic = trim($_POST["discussion_topic"]);
}

// Validate message
if (empty(trim($_POST["message"]))) {
$message_err = "Please enter a message.";
} else {
$message = trim($_POST["message"]);
}

 if (empty($discussion_topic_err) && empty($message_err)) {
if (isset($_POST['discussion_id']) && !empty($_POST['discussion_id'])) {
 $sql = "UPDATE discussions SET discussion_topic = ?, message = ? WHERE discussion_id = ? AND created_by = ?";
if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("ssii", $discussion_topic, $message, $_POST['discussion_id'], $_SESSION["user_id"]);
$stmt->execute();
$stmt->close();
}
} else {
 $sql = "INSERT INTO discussions (discussion_topic, message, created_by) VALUES (?, ?, ?)";
if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("ssi", $discussion_topic, $message, $_SESSION["user_id"]);
$stmt->execute();
$stmt->close();
}
}
header("location: discussions.php");
exit();
}
}

 if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
$sql = "DELETE FROM discussions WHERE discussion_id = ? AND created_by = ?";
if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("ii", $_GET['delete_id'], $_SESSION["user_id"]);
$stmt->execute();
$stmt->close();
header("location: discussions.php");
exit();
}
}

// Fetch all discussions
$sql = "SELECT discussions.discussion_id, discussions.discussion_topic, discussions.message, discussions.created_at, users.username
FROM discussions
JOIN users ON discussions.created_by = users.user_id
ORDER BY discussions.created_at DESC";

$result = $mysqli->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Discussions</title>
<style>
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

.btn {
display: inline-block;
padding: 8px 16px;
background-color: #DA7297;
color: #fff;
text-decoration: none;
border-radius: 4px;
margin-right: 10px;
transition: background-color 0.3s ease;
background-color: #DA7297;

}

.btn:hover {
background-color: #DA7297;
}

.form-group {
margin-bottom: 15px;
}

.has-error input,
.has-error textarea {
border-color: #dc3545 !important;
}

.has-error .help-block {
color: #dc3545;
}

.discussions {
margin-bottom: 20px;
}

.discussion-card {
border: 1px solid #ccc;
border-radius: 6px;
padding: 10px;
margin-bottom: 10px;
background-color: #fff;
}

.discussion-card h3 {
margin-top: 0;
color: #333;
}

.discussion-card p {
margin-bottom: 8px;
text-align: justify;
}

.new-discussion {
margin-top: 20px;
background-color: #f9f9f9;
padding: 15px;
border-radius: 6px;
}

.new-discussion h2 {
margin-top: 0;
font-size: 24px;
color: #333;
}

.new-discussion form {
margin-top: 10px;
}

.new-discussion label {
font-weight: bold;
display: block;
margin-bottom: 5px;
}

.new-discussion input[type="text"],
.new-discussion textarea {
width: 100%;
padding: 8px;
border: 1px solid #ccc;
border-radius: 4px;
font-size: 14px;
margin-bottom: 10px;
}

.new-discussion textarea {
resize: vertical;
min-height: 100px;
}

.new-discussion .btn-primary {
background-color: #DA7297;
color: #fff;
border: none;
padding: 10px 20px;
border-radius: 4px;
cursor: pointer;
transition: background-color 0.3s ease;
}

.new-discussion .btn-primary:hover {
background-color: #DA7297;
}

.new-discussion .help-block {
color: #dc3545;
margin-top: 5px;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="wrapper">
<h2>Discussions</h2>
<div class="discussions">
<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<div class="discussion-card">
<h3><?php echo htmlspecialchars($row['discussion_topic']); ?></h3>
<?php
$paragraphs = preg_split('/(?<=[.?!])\s+(?=[a-zA-Z])/', htmlspecialchars($row['message']));
foreach ($paragraphs as $paragraph):
?>
<p><?php echo $paragraph; ?></p>
<?php endforeach; ?>
<p><strong>Posted by:</strong> <?php echo htmlspecialchars($row['username']); ?></p>
<p><strong>Posted at:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
<?php if ($row['username'] == $_SESSION['username']): ?>
<a href="discussions.php?edit_id=<?php echo $row['discussion_id']; ?>" class="btn">Edit</a>
<a href="discussions.php?delete_id=<?php echo $row['discussion_id']; ?>" class="btn">Delete</a>
<?php endif; ?>
</div>
<?php endwhile; ?>
<?php else: ?>
<p>No discussions found.</p>
<?php endif; ?>
</div>

<div class="new-discussion">
<h2><?php echo isset($_GET['edit_id']) ? 'Edit Discussion' : 'Create New Discussion'; ?></h2>
<?php
if (isset($_GET['edit_id'])) {
$sql = "SELECT discussion_topic, message FROM discussions WHERE discussion_id = ? AND created_by = ?";
if ($stmt = $mysqli->prepare($sql)) {
$stmt->bind_param("ii", $_GET['edit_id'], $_SESSION["user_id"]);
$stmt->execute();
$stmt->bind_result($discussion_topic, $message);
$stmt->fetch();
$stmt->close();
}
}
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<div class="form-group <?php echo (!empty($discussion_topic_err)) ? 'has-error' : ''; ?>">
<label>Discussion Topic</label>
<input type="text" name="discussion_topic" class="form-control" value="<?php echo $discussion_topic; ?>">
<span class="help-block"><?php echo $discussion_topic_err; ?></span>
</div>
<div class="form-group <?php echo (!empty($message_err)) ? 'has-error' : ''; ?>">
<label>Message</label>
<textarea name="message" class="form-control"><?php echo $message; ?></textarea>
<span class="help-block"><?php echo $message_err; ?></span>
</div>
<?php if (isset($_GET['edit_id'])): ?>
<input type="hidden" name="discussion_id" value="<?php echo $_GET['edit_id']; ?>">
<?php endif; ?>
<div class="form-group">
<input type="submit" class="btn btn-primary" value="<?php echo isset($_GET['edit_id']) ? 'Update' : 'Submit'; ?>">
</div>
</form>
</div>
</div>
</body>
</html>

<?php
$mysqli->close();
?>
