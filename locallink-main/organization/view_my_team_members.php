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

// Check if project_id is set in the URL
if (!isset($_GET['project_id'])) {
    die("Error: project_id is not set.");
}

// Fetch project details based on project_id
$project_id = $_GET['project_id'];
$sql = "SELECT * FROM projects WHERE project_id = ?";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Failed to prepare project query: " . $mysqli->error);
}

$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $project = $result->fetch_assoc();
} else {
    die("Project not found.");
}

$stmt->close();

// Fetch team members for the project
$sql_team_members = "SELECT users.username
                    FROM team_members
                    JOIN users ON team_members.user_id = users.user_id
                    WHERE team_members.project_id = ?";
$stmt_team_members = $mysqli->prepare($sql_team_members);

if (!$stmt_team_members) {
    die("Failed to prepare team members query: " . $mysqli->error);
}

$stmt_team_members->bind_param("i", $project_id);
$stmt_team_members->execute();
$result_team_members = $stmt_team_members->get_result();

$team_members = [];
if ($result_team_members->num_rows > 0) {
    while ($row = $result_team_members->fetch_assoc()) {
        $team_members[] = [
            'username' => $row['username'],
        ];
    }
}

$stmt_team_members->close();

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Members: <?php echo htmlspecialchars($project['project_name']); ?></title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file for styling -->
    <style>
        /* Example CSS styles for team members page */
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .team-members {
            margin-bottom: 20px;
        }
        .team-members h2 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }
        .team-members p {
            font-size: 16px;
            line-height: 1.6;
            color: #666;
        }
        .team-members .members-list {
            margin-top: 10px;
        }
        .team-members .members-list ul {
            padding-left: 20px;
            margin-bottom: 0;
        }
        .team-members .members-list ul li {
            list-style-type: none;
        }
    </style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

    <div class="container">
        <div class="team-members">
            <h2>Team Members for <?php echo htmlspecialchars($project['project_name']); ?></h2>
            <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($project['project_description'])); ?></p>
            <p><strong>Venue:</strong> <?php echo htmlspecialchars($project['venue']); ?></p>
            <p><strong>Current Team Members:</strong> <?php echo count($team_members); ?></p>
            <div class="members-list">
                <p><strong>Team Members:</strong></p>
                <ul>
                    <?php foreach ($team_members as $member): ?>
                        <li><?php echo htmlspecialchars($member['username']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <p><a href="projects_feed.php">Back to Projects Feed</a></p>
    </div>
</body>
</html>
