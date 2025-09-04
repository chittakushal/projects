<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-700 mb-6">Welcome to Your Dashboard</h1>

        <div class="bg-white shadow-md rounded-lg p-6 mb-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Links</h2>
            <ul class="space-y-4">
                <li>
                    <a href="hackathons.php" class="flex items-center text-blue-500 hover:text-blue-600">
                        <i class="fas fa-flag-checkered mr-3"></i> Hackathons
                    </a>
                </li>
                <li>
                    <a href="my_hackathons.php" class="flex items-center text-blue-500 hover:text-blue-600">
                        <i class="fas fa-trophy mr-3"></i> My Hackathons
                    </a>
                </li>
                <li>
                    <a href="requests.php" class="flex items-center text-blue-500 hover:text-blue-600">
                        <span>🔗</span> Requests
                    </a>
                </li>
               
                <!-- Add more links as needed -->
            </ul>
        </div>
    </div>
</body>
</html>
