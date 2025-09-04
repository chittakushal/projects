<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-orange-50 min-h-screen flex flex-col">
    <?php include 'navbar.php'; ?>
    <div class="container mx-auto px-4 mt-8">
        <h1 class="text-4xl font-bold text-orange-600 text-center mb-8">Organizer Dashboard</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <!-- Card 1: Create Hackathon -->
            <a href="create_hackathon.php" class="bg-white rounded-lg shadow-lg p-6 hover:bg-orange-100 transition">
                <div class="flex flex-col items-center">
                    <div class="bg-orange-200 p-4 rounded-full mb-4">
                        <i class="fas fa-plus-circle text-orange-600 text-4xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-orange-700">Create Hackathon</h2>
                </div>
            </a>

            <!-- Card 2: Manage Hackathons -->
            <a href="manage_hackathons.php" class="bg-white rounded-lg shadow-lg p-6 hover:bg-orange-100 transition">
                <div class="flex flex-col items-center">
                    <div class="bg-orange-200 p-4 rounded-full mb-4">
                        <i class="fas fa-tasks text-orange-600 text-4xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-orange-700">Manage Hackathons</h2>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
