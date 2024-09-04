<?php
// Initialize session
session_start();

// Check if the user is logged in and has the role 'business'
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'business') {
    header("location: ../utils/login.php");
    exit;
}

// Include the database connection file
require_once('../utils/config.php');

// Check if event_id parameter is present in the URL
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // First, delete related records from event_registrations table
    $sql_delete_registrations = "DELETE FROM event_registrations WHERE event_id = ?";
    $stmt_delete_registrations = $mysqli->prepare($sql_delete_registrations);
    $stmt_delete_registrations->bind_param("i", $event_id);

    if ($stmt_delete_registrations->execute()) {
        // Then, delete the event itself
        $sql_delete_event = "DELETE FROM events WHERE event_id = ? AND owner_id = ?";
        $stmt_delete_event = $mysqli->prepare($sql_delete_event);
        $stmt_delete_event->bind_param("ii", $event_id, $_SESSION['user_id']);

        if ($stmt_delete_event->execute()) {
            // Event deleted successfully, redirect to my_events.php
            header("location: my_events.php");
            exit();
        } else {
            echo "Error deleting event: " . $mysqli->error;
        }

        $stmt_delete_event->close();
    } else {
        echo "Error deleting event registrations: " . $mysqli->error;
    }

    $stmt_delete_registrations->close();

    // Close connection
    $mysqli->close();
} else {
    // Redirect to my_events.php if event_id is not provided
    header("location: my_events.php");
    exit();
}
?>
