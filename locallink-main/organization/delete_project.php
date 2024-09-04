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

// Get project_id from URL parameter
$project_id = $_GET['project_id'];

// Begin a transaction
$mysqli->begin_transaction();

try {
// Delete project registrations
$sql_delete_registrations = "DELETE FROM project_registrations WHERE project_id = ?";
$stmt_delete_registrations = $mysqli->prepare($sql_delete_registrations);
$stmt_delete_registrations->bind_param("i", $project_id);

// Execute the statement for project registrations deletion
if ($stmt_delete_registrations->execute()) {
$stmt_delete_registrations->close();

// Delete team members
$sql_delete_team_members = "DELETE FROM team_members WHERE project_id = ?";
$stmt_delete_team_members = $mysqli->prepare($sql_delete_team_members);
$stmt_delete_team_members->bind_param("i", $project_id);

// Execute the statement for team members deletion
if ($stmt_delete_team_members->execute()) {
$stmt_delete_team_members->close();

// Now delete the project itself
$sql_delete_project = "DELETE FROM projects WHERE project_id = ?";
$stmt_delete_project = $mysqli->prepare($sql_delete_project);
$stmt_delete_project->bind_param("i", $project_id);

// Execute the statement for project deletion
if ($stmt_delete_project->execute()) {
// Commit the transaction
$mysqli->commit();
echo "Project, team members, and associated registrations deleted successfully.";
} else {
// Rollback the transaction on failure to delete project
$mysqli->rollback();
echo "Error deleting project: " . $stmt_delete_project->error;
}

// Close project statement
$stmt_delete_project->close();
} else {
// Rollback the transaction on failure to delete team members
$mysqli->rollback();
echo "Error deleting team members: " . $stmt_delete_team_members->error;
}
} else {
// Rollback the transaction on failure to delete project registrations
$mysqli->rollback();
echo "Error deleting project registrations: " . $stmt_delete_registrations->error;
}
} catch (Exception $e) {
// Exception occurred, rollback transaction
$mysqli->rollback();
echo "Error: " . $e->getMessage();
}

// Close connection
$mysqli->close();
?>
