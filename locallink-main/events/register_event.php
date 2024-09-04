<?php
 session_start();

 if (!isset($_SESSION["user_id"])  || $_SESSION["role"] !== 'normal') {
    header("location: ../utils/login.php");
    exit;
}

 require_once('../utils/config.php');

 $user_id = $_SESSION["user_id"];

 $sql_count = "SELECT COUNT(*) AS event_count FROM event_registrations WHERE user_id = ?";
$stmt_count = $mysqli->prepare($sql_count);
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();

if ($result_count->num_rows == 1) {
    $row = $result_count->fetch_assoc();
    $event_count = $row['event_count'];
} else {
    $event_count = 0;
}

$stmt_count->close();

 $sql_events = "SELECT e.event_id, e.event_name, e.start_date, e.end_date 
               FROM events e 
               INNER JOIN event_registrations er ON e.event_id = er.event_id 
               WHERE er.user_id = ?";
$stmt_events = $mysqli->prepare($sql_events);
$stmt_events->bind_param("i", $user_id);
$stmt_events->execute();
$result_events = $stmt_events->get_result();

$registered_events = [];
while ($row_event = $result_events->fetch_assoc()) {
    $registered_events[] = $row_event;
}

$stmt_events->close();

 $mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Events</title>
    <style>
         .wrapper {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            background-color: #FFB4C2;
            color: #333;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #667BC6;
            text-align: center;
        }
        h3 {
            color: #DA7297;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        li:hover {
            background-color: #EAEAEA;
        }
        li strong {
            color: #667BC6;
        }
        p {
            color: #333;
            text-align: center;
        }
        @media screen and (max-width:800px) {
            .wrapper {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
            background-color: #FFB4C2;
            color: #333;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        }
    </style>
</head>
<body>
    <?php include('../utils/navbar.php'); ?>
    <h2>Registered Events</h2>
    <p>You are registered for <?php echo $event_count; ?> events.</p>

    <div class="wrapper">
        
         

        <?php if (!empty($registered_events)): ?>
             <ul>
                <?php foreach ($registered_events as $event): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($event['event_name']); ?></strong><br>
                        <strong>Start Date:</strong> <?php echo htmlspecialchars($event['start_date']); ?><br>
                        <strong>End Date:</strong> <?php echo htmlspecialchars($event['end_date']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You are not registered for any events.</p>
        <?php endif; ?>
    </div>
</body>
</html>
