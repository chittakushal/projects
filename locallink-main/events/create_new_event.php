<?php
session_start();

// Redirect to login if user is not logged in or not a business user
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'business') {
    header("location: ../utils/login.php");
    exit;
}

require_once('../utils/config.php');

// Initialize variables to store form data and error messages
$event_name = $event_description = $start_date = $end_date = $venue = $event_contact_email = "";
$event_name_err = $event_description_err = $start_date_err = $end_date_err = $venue_err = $event_contact_email_err = $cover_image_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate event name
    if (empty(trim($_POST["event_name"]))) {
        $event_name_err = "Please enter an event name.";
    } else {
        $event_name = trim($_POST["event_name"]);
    }

    // Validate event description
    if (empty(trim($_POST["event_description"]))) {
        $event_description_err = "Please enter a description for the event.";
    } else {
        $event_description = trim($_POST["event_description"]);
    }

    // Validate start date
    if (empty(trim($_POST["start_date"]))) {
        $start_date_err = "Please enter a start date.";
    } else {
        $start_date = trim($_POST["start_date"]);
        $current_date = new DateTime();
        $start_date_dt = new DateTime($start_date);
        if ($start_date_dt <= $current_date->modify('+1 day')) {
            $start_date_err = "Start date must be at least one day in the future.";
        }
    }

    // Validate end date
    if (empty(trim($_POST["end_date"]))) {
        $end_date_err = "Please enter an end date.";
    } else {
        $end_date = trim($_POST["end_date"]);
        $end_date_dt = new DateTime($end_date);
        if ($start_date_dt >= $end_date_dt) {
            $end_date_err = "End date must be after the start date.";
        }
    }

    // Validate venue
    if (empty(trim($_POST["venue"]))) {
        $venue_err = "Please enter a venue for the event.";
    } else {
        $venue = trim($_POST["venue"]);
    }

    // Validate event contact email
    if (empty(trim($_POST["event_contact_email"]))) {
        $event_contact_email_err = "Please enter a contact email.";
    } else {
        $event_contact_email = trim($_POST["event_contact_email"]);
        if (!filter_var($event_contact_email, FILTER_VALIDATE_EMAIL)) {
            $event_contact_email_err = "Invalid email format.";
        }
    }

    // Handle file upload for cover image
    if (!empty($_FILES["cover_image"]["tmp_name"])) {
        // Validate file type if needed
    } else {
        $cover_image_err = "Please upload a cover image.";
    }

    // Check if there are no errors before inserting into database
    if (empty($event_name_err) && empty($event_description_err) && empty($start_date_err) && empty($end_date_err) && empty($venue_err) && empty($event_contact_email_err) && empty($cover_image_err)) {
        // Prepare SQL statement
        $sql = "INSERT INTO events (event_name, event_description, cover_image, start_date, end_date, owner_id, venue, event_contact_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind parameters to the prepared statement
            $stmt->bind_param("ssssisss", $param_event_name, $param_event_description, $param_cover_image, $param_start_date, $param_end_date, $param_owner_id, $param_venue, $param_event_contact_email);

            // Set parameters
            $param_event_name = $event_name;
            $param_event_description = $event_description;
            $param_cover_image = file_get_contents($_FILES["cover_image"]["tmp_name"]); // Assuming cover_image is required
            $param_start_date = $start_date;
            $param_end_date = $end_date;
            $param_owner_id = $_SESSION["user_id"];
            $param_venue = $venue;
            $param_event_contact_email = $event_contact_email;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to business dashboard on success
                header("location: ../business/business_dashboard.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $mysqli->error;
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
<title>Create New Event</title>
<link rel="stylesheet" href="../styles.css"> <!-- Link to your CSS file for styling -->
<style>
.wrapper {
    width: 600px;
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
.form-group input[type="file"] {
    margin-top: 5px;
}
.btn-primary {
    padding: 10px 15px;
    background-color: #DA7297;
    color: white;
    border: none;
    cursor: pointer;
}
.btn-primary:hover {
    background-color: #DA7297;
}
</style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>
<br>
<br>
<div class="wrapper">
    <h2>Create New Event</h2>
    <p>Please fill this form to create an event.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group <?php echo (!empty($event_name_err)) ? 'has-error' : ''; ?>">
            <label>Event Name</label>
            <input type="text" name="event_name" class="form-control" value="<?php echo $event_name; ?>">
            <span class="help-block"><?php echo $event_name_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($event_description_err)) ? 'has-error' : ''; ?>">
            <label>Event Description</label>
            <textarea name="event_description" class="form-control" rows="5"><?php echo $event_description; ?></textarea>
            <span class="help-block"><?php echo $event_description_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($cover_image_err)) ? 'has-error' : ''; ?>">
            <label>Cover Image</label>
            <input type="file" name="cover_image" class="form-control">
            <span class="help-block"><?php echo $cover_image_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($start_date_err)) ? 'has-error' : ''; ?>">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
            <span class="help-block"><?php echo $start_date_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($end_date_err)) ? 'has-error' : ''; ?>">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
            <span class="help-block"><?php echo $end_date_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($venue_err)) ? 'has-error' : ''; ?>">
            <label>Venue</label>
            <input type="text" name="venue" class="form-control" value="<?php echo $venue; ?>">
            <span class="help-block"><?php echo $venue_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($event_contact_email_err)) ? 'has-error' : ''; ?>">
            <label>Contact Email</label>
            <input type="email" name="event_contact_email" class="form-control" value="<?php echo $event_contact_email; ?>">
            <span class="help-block"><?php echo $event_contact_email_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Create Event">
            <br><br>
            <a href="../business/business_dashboard.php" class="btn btn-secondary ml-2">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
