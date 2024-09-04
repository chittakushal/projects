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

// Check if project_id is set in the URL
if (!isset($_GET['project_id'])) {
    die("Error: project_id is not set.");
}

// Define variables and initialize with empty values
$project_id = $_GET['project_id'];

// Fetch project details
$sql_project = "SELECT project_name FROM projects WHERE project_id = ?";
$stmt_project = $mysqli->prepare($sql_project);
$stmt_project->bind_param("i", $project_id);
$stmt_project->execute();
$result_project = $stmt_project->get_result();

if ($result_project->num_rows > 0) {
    $project = $result_project->fetch_assoc();

    // Fetch team members for the project
    $sql_team_members = "
        SELECT u.username, u.email, tm.is_leader
        FROM team_members tm
        JOIN users u ON tm.user_id = u.user_id
        WHERE tm.project_id = ?";
    $stmt_team_members = $mysqli->prepare($sql_team_members);
    $stmt_team_members->bind_param("i", $project_id);
    $stmt_team_members->execute();
    $result_team_members = $stmt_team_members->get_result();

    $team_members = [];
    if ($result_team_members->num_rows > 0) {
        while ($row = $result_team_members->fetch_assoc()) {
            $team_members[] = $row;
        }
    } else {
        die("No team members found for this project.");
    }

    $stmt_team_members->close();
} else {
    die("Unauthorized access or project not found.");
}

$stmt_project->close();

 $mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Members for <?php echo htmlspecialchars($project['project_name']); ?></title>
   
</head>
<body>
<?php include('../utils/navbar.php'); ?>

    <h1>Team Members for <?php echo htmlspecialchars($project['project_name']); ?></h1>
    <div class="container">
        <?php if (!empty($team_members)): ?>
            <?php foreach ($team_members as $member): ?>
                <div class="card">
                    <div class="content">
                        <h3><?php echo htmlspecialchars($member['username']); ?></h3>
                        <p>Email: <?php echo htmlspecialchars($member['email']); ?></p>
                        <p>Is Leader: <?php echo ($member['is_leader'] == 1) ? 'Yes' : 'No'; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No team members found for this project.</p>
        <?php endif; ?>
    </div>
    <p><a href="my_projects.php">Back to My Projects</a></p>
</body>
</html>
