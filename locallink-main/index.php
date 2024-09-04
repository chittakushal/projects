<!-- login.php -->

<?php
// Include the database connection file
require_once('./utils/config.php');

// Initialize session
session_start();

// Check if the user is already logged in, redirect to respective dashboard
if (isset($_SESSION["user_id"], $_SESSION["username"], $_SESSION["role"])) {
    switch ($_SESSION["role"]) {
        case 'normal':
            header("location: /local/normal/normal_dashboard.php"); // Adjusted path
            exit();
        case 'admin':
            header("location: /local/admin/admin_dashboard.php"); // Adjusted path
            exit();
        case 'organization':
            header("location: /local/organization/organization_dashboard.php"); // Adjusted path
            exit();
        case 'business':
            header("location: /local/business/business_dashboard.php"); // Adjusted path
            exit();
        default:
            header("location: /local/index.php"); // Default redirect if role is not recognized
            exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Community</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .containerr {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            border-radius: 5px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #666;
        }
        .btns {
            display: inline-block;
            padding: 10px 20px;
            background-color: #DA7297;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btns:hover {
            background-color: #DA7297;
            text-decoration:none;
            color:white;
        }
    </style>
</head>
<body>
    <?php include('./utils/navbar.php'); ?>

    <div class="containerr">
        <h1>Welcome to the Local Community Engagement Platform</h1>
        <p>Our platform aims to connect community members, local businesses, and organizations to foster stronger community ties through:</p>
        <ul>
            <li>Sharing news and updates about local events and activities.</li>
            <li>Organizing volunteer opportunities to contribute to community causes.</li>
            <li>Encouraging collaboration on community projects for collective impact.</li>
        </ul>
        <p>Join us in making a difference in our community!</p>
        <a href="./utils/login.php" class="btns">Get Started</a>
    </div>
</body>
</html>
