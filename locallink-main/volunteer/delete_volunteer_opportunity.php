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

// Function to delete volunteer applications associated with a volunteer opportunity
function deleteVolunteerApplications($volunteer_id, $mysqli) {
    // Prepare the SQL statement
    $sql_delete_applications = "DELETE FROM volunteer_applications WHERE volunteer_id = ?";
    $stmt_delete = $mysqli->prepare($sql_delete_applications);

    if ($stmt_delete === false) {
        // Handle SQL prepare error
        die('MySQL prepare error: ' . htmlspecialchars($mysqli->error));
    }

    // Bind parameter and execute the statement
    $stmt_delete->bind_param("i", $volunteer_id);
    $stmt_execute = $stmt_delete->execute();

    if ($stmt_execute === false) {
        // Handle SQL execute error
        die('Execute error: ' . htmlspecialchars($stmt_delete->error));
    }

    // Close statement
    $stmt_delete->close();
}

// Check if volunteer_id is provided via GET parameter
if (isset($_GET['volunteer_id'])) {
    $volunteer_id = $_GET['volunteer_id'];

    // Delete related volunteer applications first
    deleteVolunteerApplications($volunteer_id, $mysqli);

    // Prepare the SQL statement to delete the volunteer opportunity
    $sql_delete_volunteer = "DELETE FROM volunteer_opportunities WHERE volunteer_id = ?";
    $stmt_delete_volunteer = $mysqli->prepare($sql_delete_volunteer);

    if ($stmt_delete_volunteer === false) {
        // Handle SQL prepare error
        die('MySQL prepare error: ' . htmlspecialchars($mysqli->error));
    }

    // Bind parameter and execute the statement
    $stmt_delete_volunteer->bind_param("i", $volunteer_id);
    $stmt_execute = $stmt_delete_volunteer->execute();

    if ($stmt_execute === false) {
        // Handle SQL execute error
        die('Execute error: ' . htmlspecialchars($stmt_delete_volunteer->error));
    }

    // Close statement
    $stmt_delete_volunteer->close();

    // Redirect to the manage volunteer opportunities page
    header("Location:manage_volunteers.php");
    exit();
} else {
    // If volunteer_id is not provided, redirect to manage volunteer opportunities page
    header("Location: manage_volunteers.php");
    exit();
}

// Close connection
$mysqli->close();
?>
