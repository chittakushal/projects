<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'normal') {
    header("location: ../utils/login.php");
    exit;
}

require_once('../utils/config.php');

if (!isset($_GET['project_id'])) {
    die("Error: project_id is not set.");
}

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

$sql_normal_users = "SELECT users.user_id, users.username
                     FROM users
                     WHERE users.user_id NOT IN (
                         SELECT user_id FROM team_members WHERE project_id = ?
                     ) AND users.role = 'normal'";
$stmt_normal_users = $mysqli->prepare($sql_normal_users);

if (!$stmt_normal_users) {
    die("Failed to prepare normal users query: " . $mysqli->error);
}

$stmt_normal_users->bind_param("i", $project_id);
$stmt_normal_users->execute();
$result_normal_users = $stmt_normal_users->get_result();

$normal_users = [];
if ($result_normal_users->num_rows > 0) {
    while ($row = $result_normal_users->fetch_assoc()) {
        $normal_users[$row['user_id']] = [
            'username' => htmlspecialchars($row['username']),
            'name' => 'Not provided'
        ];
    }
}

$stmt_normal_users->close();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $sql_check_team_member = "SELECT COUNT(*) AS count FROM team_members WHERE project_id = ? AND user_id = ?";
    $stmt_check_team_member = $mysqli->prepare($sql_check_team_member);

    if (!$stmt_check_team_member) {
        die("Failed to prepare check team member query: " . $mysqli->error);
    }

    $stmt_check_team_member->bind_param("ii", $project_id, $user_id);
    $stmt_check_team_member->execute();
    $result_check_team_member = $stmt_check_team_member->get_result();
    $count = $result_check_team_member->fetch_assoc()['count'];
    $stmt_check_team_member->close();

    if ($count > 0) {
        $message = "<p>Error: Selected user is already a team member.</p>";
    } else {
        $insert_sql = "INSERT INTO team_members (project_id, user_id) VALUES (?, ?)";
        $insert_stmt = $mysqli->prepare($insert_sql);

        if (!$insert_stmt) {
            die("Failed to prepare insert team member query: " . $mysqli->error);
        }

        $insert_stmt->bind_param("ii", $project_id, $user_id);

        if ($insert_stmt->execute()) {
            $message = "<p>Team member added successfully!</p>";
            unset($normal_users[$user_id]);
        } else {
            $message = "<p>Error adding team member: " . $mysqli->error . "</p>";
        }

        $insert_stmt->close();
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Team Members: <?php echo htmlspecialchars($project['project_name']); ?></title>
<link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file for styling -->
<style>
.containerr {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
}
.add-members {
    margin-bottom: 20px;
}
.add-members h2 {
    margin-top: 0;
    font-size: 24px;
    color: #333;
}
.add-members p {
    font-size: 16px;
    line-height: 1.6;
    color: #666;
}
.add-members .form-group {
    margin-bottom: 10px;
}
.add-members label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}
.add-members select {
    width: 100%;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
.add-members .btn-container {
    margin-top: 10px;
}
.add-members .btn {
    display: inline-block;
    padding: 8px 16px;
    background-color: #007BFF;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s;
    margin-right: 10px;
}
.add-members .btn:hover {
    background-color: #0056b3;
}
.add-members .btn-disabled {
    background-color: #ccc;
    cursor: not-allowed;
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
    <div class="add-members">
        <h2>Add Team Members for <?php echo htmlspecialchars($project['project_name']); ?></h2>
        <?php echo $message; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?project_id=" . $project_id; ?>" method="post">
            <div class="form-group">
                <label for="user_id">Select User:</label>
                <select name="user_id" id="user_id">
                    <option value="">Select a user</option>
                    <?php foreach ($normal_users as $user_id => $user): ?>
                        <option value="<?php echo $user_id; ?>"><?php echo htmlspecialchars($user['username']) . ' (' . htmlspecialchars($user['name']) . ')'; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" value="Add Team Member" <?php echo empty($normal_users) ? 'disabled' : ''; ?>>
            </div>
        </form>
    </div>
    <p><a href="projects_feed.php">Back to Projects Feed</a></p>
</div>
</body>
</html>
