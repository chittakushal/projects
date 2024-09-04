<?php
session_start();

// Redirect to login if user is not logged in or not an organization user
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'organization') {
    header("location: ../utils/login.php");
    exit;
}

require_once('../utils/config.php');

$project_name = $project_description = $team_members_limit = $venue = $contact_email = "";
$project_name_err = $project_description_err = $team_members_limit_err = $venue_err = $contact_email_err = $cover_image_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate project name
    if (empty(trim($_POST["project_name"]))) {
        $project_name_err = "Please enter a project name.";
    } else {
        $project_name = trim($_POST["project_name"]);
    }

    // Validate project description
    if (empty(trim($_POST["project_description"]))) {
        $project_description_err = "Please enter a project description.";
    } else {
        $project_description = trim($_POST["project_description"]);
    }

    // Validate team members limit
    if (empty(trim($_POST["team_members_limit"]))) {
        $team_members_limit_err = "Please enter a team members limit.";
    } elseif (!ctype_digit(trim($_POST["team_members_limit"]))) {
        $team_members_limit_err = "Please enter a positive integer value.";
    } else {
        $team_members_limit = trim($_POST["team_members_limit"]);
    }

    // Validate venue
    if (empty(trim($_POST["venue"]))) {
        $venue_err = "Please enter a venue.";
    } else {
        $venue = trim($_POST["venue"]);
    }

    // Validate contact email
    if (empty(trim($_POST["contact_email"]))) {
        $contact_email_err = "Please enter a contact email.";
    } elseif (!filter_var(trim($_POST["contact_email"]), FILTER_VALIDATE_EMAIL)) {
        $contact_email_err = "Please enter a valid email address.";
    } else {
        $contact_email = trim($_POST["contact_email"]);
    }

    // Handle file upload for cover image
    if (!empty($_FILES["cover_image"]["tmp_name"])) {
        $cover_image = file_get_contents($_FILES["cover_image"]["tmp_name"]);
    } else {
        $cover_image_err = "Please upload a cover image.";
    }

    // Check if there are no errors before inserting into database
    if (empty($project_name_err) && empty($project_description_err) && empty($team_members_limit_err) && empty($venue_err) && empty($contact_email_err) && empty($cover_image_err)) {
        // Prepare SQL statement
        $sql = "INSERT INTO projects (project_name, project_description, team_members_limit, current_team_members, owner_id, venue, cover_image, contact_email) VALUES (?, ?, ?, 0, ?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind parameters to the prepared statement
            $stmt->bind_param("ssissss", $project_name, $project_description, $team_members_limit, $_SESSION["user_id"], $venue, $cover_image, $contact_email);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to organization dashboard on success
                header("location: organization_dashboard.php");
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
    <title>Create Project</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFFFFF;
            color: #333333;
        }
        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #FFFFFF;
            border: 1px solid #CCCCCC;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            color: #667BC6;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #CCCCCC;
            border-radius: 4px;
        }
        .form-control:focus {
            border-color: #667BC6;
            outline: none;
        }
        .help-block {
            color: #DA7297;
            font-size: 12px;
        }
        .btn-primary {
            background-color: #FFB4C2;
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-primary:hover {
            background-color: #DA7297;
        }
        .has-error .form-control {
            border-color: #DA7297;
        }
        .has-error .help-block {
            color: #DA7297;
        }
    </style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>
<br><br><br>
<div class="wrapper">
    <h2 style="color: #667BC6;">Create New Project</h2>
    <p>Please fill this form to create a new project.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group <?php echo (!empty($project_name_err)) ? 'has-error' : ''; ?>">
            <label>Project Name</label>
            <input type="text" name="project_name" class="form-control" value="<?php echo $project_name; ?>">
            <span class="help-block"><?php echo $project_name_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($project_description_err)) ? 'has-error' : ''; ?>">
            <label>Project Description</label>
            <textarea rows="5" name="project_description" class="form-control"><?php echo $project_description; ?></textarea>
            <span class="help-block"><?php echo $project_description_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($team_members_limit_err)) ? 'has-error' : ''; ?>">
            <label>Team Members Limit</label>
            <input type="text" name="team_members_limit" class="form-control" value="<?php echo $team_members_limit; ?>">
            <span class="help-block"><?php echo $team_members_limit_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($venue_err)) ? 'has-error' : ''; ?>">
            <label>Venue</label>
            <input type="text" name="venue" class="form-control" value="<?php echo $venue; ?>">
            <span class="help-block"><?php echo $venue_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($contact_email_err)) ? 'has-error' : ''; ?>">
            <label>Contact Email</label>
            <input type="text" name="contact_email" class="form-control" value="<?php echo $contact_email; ?>">
            <span class="help-block"><?php echo $contact_email_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($cover_image_err)) ? 'has-error' : ''; ?>">
            <label>Cover Image</label>
            <input type="file" name="cover_image" class="form-control">
            <span class="help-block"><?php echo $cover_image_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
        </div>
    </form>
</div>
</body>
</html>
