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

// Define variables and initialize with empty values
$project_name = $project_description = $contact_email = "";
$project_name_err = $project_description_err = $team_members_limit_err = $contact_email_err = $team_member_user_id_err = "";
$project_id = $_GET['project_id'];
$team_members_limit = 0;

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'];
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    $team_members_limit = $_POST['team_members_limit'];
    $team_member_user_id = $_POST['team_member_user_id'];
    $contact_email = $_POST['contact_email'];

    // Check if a file is uploaded for cover image
    if (!empty($_FILES['cover_image']['name'])) {
        $cover_image = file_get_contents($_FILES['cover_image']['tmp_name']);

        // Prepare an update statement for project details including cover image
        $sql = "UPDATE projects SET project_name = ?, project_description = ?, team_members_limit = ?, cover_image = ?, contact_email = ? WHERE project_id = ? AND owner_id = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssisssi", $project_name, $project_description, $team_members_limit, $cover_image, $contact_email, $project_id, $_SESSION["user_id"]);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Check if a new team member is being added
                if (!empty($team_member_user_id)) {
                    // Check the current number of team members
                    $sql_count = "SELECT COUNT(*) AS member_count FROM team_members WHERE project_id = ?";
                    $stmt_count = $mysqli->prepare($sql_count);
                    $stmt_count->bind_param("i", $project_id);
                    $stmt_count->execute();
                    $result_count = $stmt_count->get_result();
                    $row_count = $result_count->fetch_assoc();
                    $current_team_members = $row_count['member_count'];

                    // Check if the limit is not exceeded
                    if ($current_team_members < $team_members_limit) {
                        // Add the new team member
                        $sql_insert = "INSERT INTO team_members (project_id, user_id) VALUES (?, ?)";
                        $stmt_insert = $mysqli->prepare($sql_insert);
                        $stmt_insert->bind_param("ii", $project_id, $team_member_user_id);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                    } else {
                        echo "Team members limit reached.";
                    }
                    $stmt_count->close();
                }
                // Project updated successfully
                header("location: my_projects.php");
                exit;
            } else {
                echo "Error updating project: " . $mysqli->error;
            }
        }
    } else {
        // Prepare an update statement for project details excluding cover image
        $sql = "UPDATE projects SET project_name = ?, project_description = ?, team_members_limit = ?, contact_email = ? WHERE project_id = ? AND owner_id = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssissi", $project_name, $project_description, $team_members_limit, $contact_email, $project_id, $_SESSION["user_id"]);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Check if a new team member is being added
                if (!empty($team_member_user_id)) {
                    // Check the current number of team members
                    $sql_count = "SELECT COUNT(*) AS member_count FROM team_members WHERE project_id = ?";
                    $stmt_count = $mysqli->prepare($sql_count);
                    $stmt_count->bind_param("i", $project_id);
                    $stmt_count->execute();
                    $result_count = $stmt_count->get_result();
                    $row_count = $result_count->fetch_assoc();
                    $current_team_members = $row_count['member_count'];

                    // Check if the limit is not exceeded
                    if ($current_team_members < $team_members_limit) {
                        // Add the new team member
                        $sql_insert = "INSERT INTO team_members (project_id, user_id) VALUES (?, ?)";
                        $stmt_insert = $mysqli->prepare($sql_insert);
                        $stmt_insert->bind_param("ii", $project_id, $team_member_user_id);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                    } else {
                        echo "Team members limit reached.";
                    }
                    $stmt_count->close();
                }
                // Project updated successfully
                header("location: my_projects.php");
                exit;
            } else {
                echo "Error updating project: " . $mysqli->error;
            }
        }
    }

    // Close statement
    $stmt->close();
}

// Retrieve project details for display
if (isset($project_id)) {
    // Prepare a select statement
    $sql = "SELECT project_name, project_description, team_members_limit, contact_email FROM projects WHERE project_id = ? AND owner_id = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("ii", $project_id, $_SESSION["user_id"]);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            // Check if the project exists
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $project_name = $row['project_name'];
                $project_description = $row['project_description'];
                $team_members_limit = $row['team_members_limit'];
                $contact_email = $row['contact_email'];
            } else {
                echo "No project found.";
            }
        } else {
            echo "Error executing query: " . $mysqli->error;
        }

        // Close statement
        $stmt->close();
    }
} else {
    echo "No project ID specified.";
}

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Project</title>
<style>
    /* CSS styles using the specified color palette */
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

<div class="wrapper">
    <h2 style="color: #667BC6;">Update Project</h2>
    <p>Please fill this form to update the project.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
        <div class="form-group <?php echo (!empty($project_name_err)) ? 'has-error' : ''; ?>">
            <label>Project Name</label>
            <input type="text" name="project_name" class="form-control" value="<?php echo htmlspecialchars($project_name); ?>" required>
            <span class="help-block"><?php echo $project_name_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($project_description_err)) ? 'has-error' : ''; ?>">
            <label>Project Description</label>
            <textarea name="project_description" class="form-control" required><?php echo htmlspecialchars($project_description); ?></textarea>
            <span class="help-block"><?php echo $project_description_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($team_members_limit_err)) ? 'has-error' : ''; ?>">
            <label>Team Members Limit</label>
            <input type="number" name="team_members_limit" class="form-control" value="<?php echo htmlspecialchars($team_members_limit); ?>" required>
            <span class="help-block"><?php echo $team_members_limit_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($contact_email_err)) ? 'has-error' : ''; ?>">
            <label>Contact Email</label>
            <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($contact_email); ?>" required>
            <span class="help-block"><?php echo $contact_email_err; ?></span>
        </div>
        <div class="form-group">
            <label>Cover Image</label>
            <input type="file" name="cover_image" class="form-control">
        </div>
      
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a class="btn btn-link" href="my_projects.php">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
