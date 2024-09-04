<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'business') {
    header("location: ../utils/login.php");
    exit;
}

require_once('../utils/config.php');

$event_name = $event_description = $venue = $email = "";
$event_name_err = $event_description_err = $venue_err = $email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["event_name"]))) {
        $event_name_err = "Please enter an event name.";
    } else {
        $event_name = trim($_POST["event_name"]);
    }

    if (empty(trim($_POST["event_description"]))) {
        $event_description_err = "Please enter an event description.";
    } else {
        $event_description = trim($_POST["event_description"]);
    }

    if (empty(trim($_POST["venue"]))) {
        $venue_err = "Please enter a venue for the event.";
    } else {
        $venue = trim($_POST["venue"]);
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter a contact email.";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        }
    }

    if (empty(trim($_POST["event_id"]))) {
        echo "Invalid request.";
        exit();
    } else {
        $event_id = $_POST["event_id"];
    }

    if (empty($event_name_err) && empty($event_description_err) && empty($venue_err) && empty($email_err)) {
        if (!empty($_FILES['cover_image']['name'])) {
            $cover_image = file_get_contents($_FILES['cover_image']['tmp_name']);

            $sql = "UPDATE events SET event_name = ?, event_description = ?, cover_image = ?, venue = ?, event_contact_email = ? WHERE event_id = ? AND owner_id = ?";

            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("ssssssi", $event_name, $event_description, $cover_image, $venue, $email, $event_id, $_SESSION['user_id']);

                if ($stmt->execute()) {
                    $stmt->close();
                    header("location: my_events.php");
                    exit();
                } else {
                    echo "Error updating event: " . $mysqli->error;
                }
            }
        } else {
            $sql = "UPDATE events SET event_name = ?, event_description = ?, venue = ?, event_contact_email = ? WHERE event_id = ? AND owner_id = ?";

            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("ssssii", $event_name, $event_description, $venue, $email, $event_id, $_SESSION['user_id']);

                if ($stmt->execute()) {
                    $stmt->close();
                    header("location: my_events.php");
                    exit();
                } else {
                    echo "Error updating event: " . $mysqli->error;
                }
            }
        }
    }

    $mysqli->close();
} else {
    if (isset($_GET['event_id'])) {
        $event_id = $_GET['event_id'];

        $sql = "SELECT * FROM events WHERE event_id = ? AND owner_id = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ii", $event_id, $_SESSION['user_id']);

            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $event = $result->fetch_assoc();
                    $event_name = $event['event_name'];
                    $event_description = $event['event_description'];
                    $venue = $event['venue'];
                    $email = $event['event_contact_email'];
                } else {
                    header("location: my_events.php");
                    exit();
                }
            } else {
                echo "Error fetching event details: " . $mysqli->error;
            }

            $stmt->close();
        }
    } else {
        header("location: my_events.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Event</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 20px auto;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            border-radius: 5px;
            padding: 20px;
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
        .btn-primary {
            padding: 10px 15px;
            background-color: #DA7297;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #DA7297;
        }
        .navbar {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Bottom border */
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .navbar-brand {
            font-size: 1.8rem;  
            font-weight: bold;
            color: #333; 
        }
        .navbar-toggler {
            border: none; 
        }
        .navbar-nav .nav-item {
            margin-left: 15px;  
        }
        .navbar-nav .nav-link {
            color: #333;  
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            color: #007bff;  
        }
        .navbar-collapse {
            justify-content: space-between;  
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/local/index.php">LocalLink</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../business/business_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../utils/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2>Update Event</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Event Name</label>
            <input type="text" name="event_name" class="form-control" value="<?php echo htmlspecialchars($event_name); ?>">
            <span class="help-block"><?php echo $event_name_err; ?></span>
        </div>
        <div class="form-group">
            <label>Event Description</label>
            <textarea rows="10" name="event_description" class="form-control"><?php echo htmlspecialchars($event_description); ?></textarea>
            <span class="help-block"><?php echo $event_description_err; ?></span>
        </div>
        <div class="form-group">
            <label>Venue</label>
            <input type="text" name="venue" class="form-control" value="<?php echo htmlspecialchars($venue); ?>">
            <span class="help-block"><?php echo $venue_err; ?></span>
        </div>
        <div class="form-group">
            <label>Contact Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
            <span class="help-block"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group">
            <label>Cover Image</label>
            <input type="file" name="cover_image">
        </div>
        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Update">
            <br>
            <br>
            <a href="my_events.php" class="btn btn-primary">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
