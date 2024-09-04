<?php
// Initialize session
session_start();

// Check if the user is logged in and is an admin, if not then redirect to login page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'admin') {
    header("location: ../utils/login.php");
    exit;
}

// Include the database connection file
require_once('../utils/config.php');

// Handle approval toggle for events
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_event'])) {
    $event_id = $_POST['event_id'];

    // Prepare the SQL statement
    $sql_update = "UPDATE events SET is_disapproved = NOT is_disapproved WHERE event_id = ?";
    $stmt_update = $mysqli->prepare($sql_update);

    if ($stmt_update === false) {
        // Handle SQL prepare error
        die('MySQL prepare error: ' . htmlspecialchars($mysqli->error));
    }

    // Bind parameters and execute the statement
    $stmt_update->bind_param("i", $event_id);
    $stmt_execute = $stmt_update->execute();

    if ($stmt_execute === false) {
        // Handle SQL execute error
        die('Execute error: ' . htmlspecialchars($stmt_update->error));
    }

    // Close statement
    $stmt_update->close();

    // Redirect after POST to prevent resubmission on refresh
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Handle approval toggle for projects
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_project'])) {
    $project_id = $_POST['project_id'];

    // Prepare the SQL statement
    $sql_update = "UPDATE projects SET is_disapproved = NOT is_disapproved WHERE project_id = ?";
    $stmt_update = $mysqli->prepare($sql_update);

    if ($stmt_update === false) {
        // Handle SQL prepare error
        die('MySQL prepare error: ' . htmlspecialchars($mysqli->error));
    }

    // Bind parameters and execute the statement
    $stmt_update->bind_param("i", $project_id);
    $stmt_execute = $stmt_update->execute();

    if ($stmt_execute === false) {
        // Handle SQL execute error
        die('Execute error: ' . htmlspecialchars($stmt_update->error));
    }

    // Close statement
    $stmt_update->close();

    // Redirect after POST to prevent resubmission on refresh
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Handle approval toggle for news
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_news'])) {
    $news_id = $_POST['news_id'];

    // Prepare the SQL statement
    $sql_update = "UPDATE news SET is_disapproved = NOT is_disapproved WHERE news_id = ?";
    $stmt_update = $mysqli->prepare($sql_update);

    if ($stmt_update === false) {
        // Handle SQL prepare error
        die('MySQL prepare error: ' . htmlspecialchars($mysqli->error));
    }

    // Bind parameters and execute the statement
    $stmt_update->bind_param("i", $news_id);
    $stmt_execute = $stmt_update->execute();

    if ($stmt_execute === false) {
        // Handle SQL execute error
        die('Execute error: ' . htmlspecialchars($stmt_update->error));
    }

    // Close statement
    $stmt_update->close();

    // Redirect after POST to prevent resubmission on refresh
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Handle approval toggle for volunteer opportunities
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_volunteer'])) {
    $volunteer_id = $_POST['volunteer_id'];

    // Prepare the SQL statement
    $sql_update = "UPDATE volunteer_opportunities SET is_disapproved = NOT is_disapproved WHERE volunteer_id = ?";
    $stmt_update = $mysqli->prepare($sql_update);

    if ($stmt_update === false) {
        // Handle SQL prepare error
        die('MySQL prepare error: ' . htmlspecialchars($mysqli->error));
    }

    // Bind parameters and execute the statement
    $stmt_update->bind_param("i", $volunteer_id);
    $stmt_execute = $stmt_update->execute();

    if ($stmt_execute === false) {
        // Handle SQL execute error
        die('Execute error: ' . htmlspecialchars($stmt_update->error));
    }

    // Close statement
    $stmt_update->close();

    // Redirect after POST to prevent resubmission on refresh
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Fetch all events for display
$sql_events = "SELECT event_id, event_name, is_disapproved FROM events";
$result_events = $mysqli->query($sql_events);

// Fetch all projects for display
$sql_projects = "SELECT project_id, project_name, is_disapproved FROM projects";
$result_projects = $mysqli->query($sql_projects);

// Fetch all news for display
$sql_news = "SELECT news_id, news_title, is_disapproved FROM news";
$result_news = $mysqli->query($sql_news);

// Fetch all volunteer opportunities for display
$sql_volunteer_opportunities = "SELECT volunteer_id, opportunity_name, is_disapproved FROM volunteer_opportunities";
$result_volunteer_opportunities = $mysqli->query($sql_volunteer_opportunities);

// Close connection
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<style>
/* CSS Styles */
* {
margin: 0;
padding: 0;
box-sizing: border-box;
}

body {
font-family: Arial, sans-serif;
background-color: #FFFAB7;
color: #333;
line-height: 1.6;
}

.containerr {
max-width: 800px;
margin: 20px auto;
padding: 20px;
background-color: #FFFFFF;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
color: #DA7297;
margin-bottom: 20px;
text-align: center;
}

h2 {
color: #DA7297;
margin-top: 30px;
margin-bottom: 15px;
}

ul {
list-style-type: none;
padding: 0;
}

ul li {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 15px;
padding: 10px;
border-radius: 4px;
background-color: #FFD1E3;
}

ul li:hover {
background-color: #FFB4C2;
}

button {
background-color: #5BBCFF;
color: #FFFFFF;
border: none;
padding: 8px 16px;
cursor: pointer;
border-radius: 4px;
transition: background-color 0.3s ease;
}

button:hover {
background-color: #FFB4C2;
}
</style>
</head>
<body>
<div class="containerr">
<?php include('../utils/navbar.php'); ?>

<h1>Admin Dashboard</h1>

 <div>
<h2>Manage Events</h2>
<?php if ($result_events->num_rows > 0): ?>
<ul>
<?php while ($event = $result_events->fetch_assoc()): ?>
<li>
<span><?php echo htmlspecialchars($event['event_name']); ?></span>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
<button type="submit" name="approve_event">
<?php echo $event['is_disapproved'] ? 'Approve' : 'Disapprove'; ?>
</button>
</form>
</li>
<?php endwhile; ?>
</ul>
<?php else: ?>
<p>No events found.</p>
<?php endif; ?>
</div>

<!-- Projects Section -->
<div>
<h2>Manage Projects</h2>
<?php if ($result_projects->num_rows > 0): ?>
<ul>
<?php while ($project = $result_projects->fetch_assoc()): ?>
<li>
<span><?php echo htmlspecialchars($project['project_name']); ?></span>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
<button type="submit" name="approve_project">
<?php echo $project['is_disapproved'] ? 'Approve' : 'Disapprove'; ?>
</button>
</form>
</li>
<?php endwhile; ?>
</ul>
<?php else: ?>
<p>No projects found.</p>
<?php endif; ?>
</div>

<!-- News Section -->
<div>
<h2>Manage News</h2>
<?php if ($result_news->num_rows > 0): ?>
<ul>
<?php while ($news = $result_news->fetch_assoc()): ?>
<li>
<span><?php echo htmlspecialchars($news['news_title']); ?></span>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<input type="hidden" name="news_id" value="<?php echo $news['news_id']; ?>">
<button type="submit" name="approve_news">
<?php echo $news['is_disapproved'] ? 'Approve' : 'Disapprove'; ?>
</button>
</form>
</li>
<?php endwhile; ?>
</ul>
<?php else: ?>
<p>No news found.</p>
<?php endif; ?>
</div>

<!-- Volunteer Opportunities Section -->
<div>
<h2>Manage Volunteer Opportunities</h2>
<?php if ($result_volunteer_opportunities->num_rows > 0): ?>
<ul>
<?php while ($opportunity = $result_volunteer_opportunities->fetch_assoc()): ?>
<li>
<span><?php echo htmlspecialchars($opportunity['opportunity_name']); ?></span>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<input type="hidden" name="volunteer_id" value="<?php echo $opportunity['volunteer_id']; ?>">
<button type="submit" name="approve_volunteer">
<?php echo $opportunity['is_disapproved'] ? 'Approve' : 'Disapprove'; ?>
</button>
</form>
</li>
<?php endwhile; ?>
</ul>
<?php else: ?>
<p>No volunteer opportunities found.</p>
<?php endif; ?>
</div>
</div>
</body>
</html>
